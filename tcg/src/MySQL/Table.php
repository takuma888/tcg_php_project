<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午12:33
 */

namespace TCG\MySQL;


class Table
{
    /**
     * @var Server
     */
    private $write;
    /**
     * @var Server
     */
    private $read;

    /**
     * Table base name
     * @var string
     */
    protected $tableBaseName = '';

    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * Database base name of this table
     * @var string
     */
    protected $dbBaseName = '';

    /**
     * @var string
     */
    protected $dbPrefix = '';

    /**
     * @var string
     */
    protected $tablePartitionField = '';

    /**
     * @var array
     */
    protected $tablePartitionTypes = [];

    /**
     * @var int
     */
    protected $tableMaxNum = 1;

    /**
     * @var int
     */
    protected $dbMaxNum = 1;

    /**
     * @var string
     */
    protected $dbPartitionField = '';

    /**
     * @var array
     */
    protected $dbPartitionTypes = [];

    /**
     * @var string
     */
    protected $createSQL = '';

    /**
     * Table constructor.
     * @param Server $writeServer
     * @param Server $readServer
     * @param string $dbBaseName
     * @throws \Exception
     */
    public function __construct(Server $writeServer, Server $readServer, $dbBaseName = '')
    {
        $this->write = $writeServer;
        $this->read = $readServer;
        $this->dbBaseName = $dbBaseName;
        $this->checkConfig();
    }

    /**
     * @throws \Exception
     */
    protected function checkConfig()
    {
        if (!$this->tableBaseName) {
            throw new \Exception("Please set the tableBaseName property not an empty value");
        }
        if (!$this->dbBaseName) {
            throw new \Exception('Please set the dbBaseName property not an empty value');
        }
    }

    /**
     * @return Server
     */
    public function getWriteServer()
    {
        return $this->write;
    }

    /**
     * @return Server
     */
    public function getReadServer()
    {
        return $this->read;
    }

    /**
     * @param array $partition
     * @return string
     */
    public function getTableName(array $partition = [])
    {
        $realTableName = $this->tableBaseName;
        if ($this->tableMaxNum > 1) {
            if (in_array(2, $this->tablePartitionTypes) || in_array(3, $this->tablePartitionTypes)) {
                // 只分表或者分库分表
                $partitionValue = false;
                if (isset($partition[$this->tablePartitionField])) {
                    $partitionValue = $partition[$this->tablePartitionField];
                }
                if ($partitionValue !== false) {
                    $tmp = false;
                    if (is_int($partitionValue)) {
                        $tmp = $partitionValue;
                    } elseif (!empty($partitionValue)) {
                        $tmp = abs(crc32(strval($partitionValue)));
                    }
                    if ($tmp !== false) {
                        $tableIndex = ($tmp / $this->dbMaxNum) % $this->tableMaxNum + 1;
                        $realTableName = $this->tableBaseName . '_' . $tableIndex;
                    }
                }
            }
        }
        if (in_array(4, $this->tablePartitionTypes)) {
            if (!isset($partition['timestamp'])) {
                $partition['timestamp'] = time();
            }
            $realTableName = $this->tableBaseName . '_' . date('Y_m_d', $partition['timestamp']);
        } elseif (in_array(5, $this->tablePartitionTypes)) {
            if (!isset($partition['timestamp'])) {
                $partition['timestamp'] = time();
            }
            $realTableName = $this->tableBaseName . '_' . date('Y_W', $partition['timestamp']);
        } elseif (in_array(6, $this->tablePartitionTypes)) {
            if (!isset($partition['timestamp'])) {
                $partition['timestamp'] = time();
            }
            $realTableName = $this->tableBaseName . '_' . date('Y_m', $partition['timestamp']);
        }
        if ($this->tablePrefix) {
            $realTableName = $this->tablePrefix . $realTableName;
        }
        return $realTableName;
    }

    /**
     * @return array
     */
    public function getTableNameRange()
    {
        $tableNames = [];
        if ($this->tableMaxNum > 1) {
            if (in_array(2, $this->tablePartitionTypes) || in_array(3, $this->tablePartitionTypes)) {
                $range = range(1, $this->tableMaxNum);
                foreach ($range as $idx) {
                    $realTableName = $this->tableBaseName . '_' . $idx;
                    if ($this->tablePrefix) {
                        $realTableName = $this->tablePrefix . $realTableName;
                    }
                    $tableNames[] = $realTableName;
                }
            }
        } else {
            $realTableName = $this->tableBaseName;
            if (in_array(4, $this->tablePartitionTypes)) {
                $realTableName = $this->tablePartitionTypes . '_' . date('Y_m_d');
            } elseif (in_array(5, $this->tablePartitionTypes)) {
                $realTableName = $this->tableBaseName . '_' . date('Y_W');
            } elseif (in_array(6, $this->tablePartitionTypes)) {
                $realTableName = $this->tableBaseName . '_' . date('Y_m');
            }
            if ($this->tablePrefix) {
                $realTableName = $this->tablePrefix . $realTableName;
            }
            $tableNames[] = $realTableName;
        }
        return $tableNames;
    }

    /**
     * @param array $partition
     * @return string
     */
    public function getDbName(array $partition = [])
    {
        $dbName = $this->dbBaseName;
        if ($this->dbMaxNum > 1) {
            if (in_array(1, $this->dbPartitionTypes) || in_array(3, $this->dbPartitionTypes)) {
                // 只分库或者分库分表
                $partitionValue = false;
                if (isset($partition[$this->dbPartitionField])) {
                    $partitionValue = $partition[$this->dbPartitionField];
                }
                if ($partitionValue !== false) {
                    $tmp = false;
                    if (is_int($partitionValue)) {
                        $tmp = $partitionValue;
                    } elseif (!empty($partitionValue)) {
                        $tmp = abs(crc32(strval($partitionValue)));
                    }
                    if ($tmp !== false) {
                        $dbIndex = intval($tmp / $this->dbMaxNum) % $this->dbMaxNum + 1;
                        $dbName = $this->dbBaseName . '_' . $dbIndex;
                    }
                }
            }
        }
        if ($this->dbPrefix) {
            $dbName = $this->dbPrefix . $dbName;
        }
        return $dbName;
    }

    /**
     * @return array
     */
    public function getDbNameRange()
    {
        $dbNames = [];
        if ($this->dbMaxNum > 1) {
            if (in_array(1, $this->dbPartitionTypes) || in_array(3, $this->dbPartitionTypes)) {
                $range = range(1, $this->dbMaxNum);
                foreach ($range as $idx) {
                    $realDbName = $this->dbBaseName . '_' . $idx;
                    if ($this->dbPrefix) {
                        $realDbName = $this->dbPrefix . $realDbName;
                    }
                    $dbNames[] = $realDbName;
                }
            }
        } else {
            $realDbName = $this->dbBaseName;
            if ($this->dbPrefix) {
                $realDbName = $this->dbPrefix . $realDbName;
            }
            $dbNames[] = $realDbName;
        }
        return $dbNames;
    }

    /**
     * @param array $partition
     * @param string $quote
     * @return string
     */
    public function getDbTableName(array $partition = [], $quote = '')
    {
        $dbName = $this->getDbName($partition);
        $tableName = $this->getTableName($partition);
        return $quote . $dbName . $quote . '.' . $quote . $tableName . $quote;
    }

    /**
     * @param string $quote
     * @return array
     */
    public function getDbTableNameRange($quote = '')
    {
        $dbTables = [];
        $dbNames = $this->getDbNameRange();
        $tableNames = $this->getTableNameRange();
        foreach ($dbNames as $dbName) {
            foreach ($tableNames as $tableName) {
                $dbTables[] = $quote . $dbName . $quote . '.' . $quote . $tableName . $quote;
            }
        }
        return $dbTables;
    }


    public function create($drop = false)
    {
        $sql = [];
        $dbNames = $this->getDbNameRange();
        $tableNames = $this->getTableNameRange();
        foreach ($dbNames as $dbName) {
            // try to create database first
            $sql[] = strtr('CREATE DATABASE IF NOT EXISTS {@database};', [
                '{@database}' => "`{$dbName}`"
            ]);
            foreach ($tableNames as $tableName) {
                if ($drop) {
                    $sql[] = strtr('DROP TABLE IF EXISTS {@table};', [
                        '{@table}' => "`{$dbName}`.`{$tableName}`",
                    ]);
                }
                $createSQL = strtr($this->createSQL, [
                    '{@table}' => "`{$dbName}`.`{$tableName}`",
                ]);
                if (substr($createSQL, -1) != ';') {
                    $createSQL .= ';';
                }
                $sql[] = $createSQL;
            }
        }
        foreach ($sql as $stmt) {
            $this->write
                ->connect()
                ->exec($stmt);
        }
    }


    public function recreate()
    {
        $this->create(true);
    }
}