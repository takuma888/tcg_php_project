<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午12:33
 */

namespace TCG\MySQL;


abstract class Table
{

    /**
     * @var Server
     */
    private $write;
    /**
     * @var Server[]
     */
    private $reads = [];

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
     * @var int
     */
    protected $tablePartitionType = 0;

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
     * @var int
     */
    protected $dbPartitionType = 0;

    /**
     * @var string
     */
    protected $createSQL = '';

    /**
     * @var string
     */
    protected $engine = '';

    /**
     * Table constructor.
     * @param Server $write
     * @param Server[] $reads
     * @param string $dbBaseName
     * @param string $tableBaseName
     */
    public function __construct(Server $write, array $reads, $dbBaseName = '', $tableBaseName = '')
    {
        $this->setWriteServer($write);
        $this->addReadServers($reads);
        if ($dbBaseName) {
            $this->setDbBaseName($dbBaseName);
        }
        if ($tableBaseName) {
            $this->setTableBaseName($tableBaseName);
        }
    }

    /**
     * @param array $partition
     * @return array
     */
    public function __invoke(array $partition = [])
    {
        return [
            'table' => $this,
            'partition' => $partition,
            'db_table_name' => $this->getDbTableName($partition, '`'),
            'db_name' => $this->getDbName($partition[$this->dbPartitionField] ?? false),
            'table_name' => $this->getTableName($partition[$this->tablePartitionField] ?? false),
        ];
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
     * @param $tableBaseName
     */
    public function setTableBaseName($tableBaseName)
    {
        $this->tableBaseName = $tableBaseName;
    }

    /**
     * @param $dbBaseName
     */
    public function setDbBaseName($dbBaseName)
    {
        $this->dbBaseName = $dbBaseName;
    }

    /**
     * @param Server $server
     */
    public function setMasterServer(Server $server)
    {
        $this->write = $server;
    }

    /**
     * @return Server
     */
    public function getMasterServer()
    {
        return $this->write;
    }

    /**
     * @param Server $server
     */
    public function addReadServer(Server $server)
    {
        $this->reads[] = $server;
    }

    /**
     * @param Server[] $servers
     */
    public function addReadServers(array $servers)
    {
        foreach ($servers as $server) {
            $this->addReadServer($server);
        }
    }

    /**
     * @return Server[]
     */
    public function getReadServers()
    {
        if ($this->reads) {
            return $this->reads;
        } else {
            return [
                $this->write
            ];
        }
    }

    /**
     * @return Server
     */
    public function getReadServer()
    {
        if ($this->reads) {
            return $this->reads[mt_rand(0, count($this->reads))];
        }
        return $this->write;
    }

    /**
     * @param Server $server
     */
    public function setWriteServer(Server $server)
    {
        $this->write = $server;
    }

    /**
     * @return Server
     */
    public function getWriteServer()
    {
        return $this->write;
    }

    /**
     * @param mixed $partitionValue
     * @param string $quote
     * @return string
     */
    public function getTableName($partitionValue = false, $quote = '')
    {
        $realTableName = $this->tableBaseName;
        if ($this->tableMaxNum > 1) {
            if ($this->tablePartitionType == 2 || $this->tablePartitionType == 3) {
                $partitionValue = false;
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
        if ($this->tablePartitionType == 4) {
            if ($partitionValue === false) {
                $partitionValue = time();
            }
            $realTableName = $this->tableBaseName . '_' . date('Y_m_d', $partitionValue);
        } elseif ($this->tablePartitionType == 5) {
            if ($partitionValue === false) {
                $partitionValue = time();
            }
            $realTableName = $this->tableBaseName . '_' . date('Y_W', $partitionValue);
        } elseif ($this->tablePartitionType == 6) {
            if ($partitionValue === false) {
                $partitionValue = time();
            }
            $realTableName = $this->tableBaseName . '_' . date('Y_m', $partitionValue);
        }
        if ($this->tablePrefix) {
            $realTableName = $this->tablePrefix . $realTableName;
        }
        return $quote . $realTableName . $quote;
    }

    /**
     * @param string $quote
     * @return array
     */
    public function getTableNameRange($quote = '')
    {
        $tableNames = [];
        if ($this->tableMaxNum > 1) {
            if ($this->tablePartitionType == 2 || $this->tablePartitionType == 3) {
                $range = range(1, $this->tableMaxNum);
                foreach ($range as $idx) {
                    $realTableName = $this->tableBaseName . '_' . $idx;
                    if ($this->tablePrefix) {
                        $realTableName = $this->tablePrefix . $realTableName;
                    }
                    $tableNames[] = $quote . $realTableName . $quote;
                }
            }
        } else {
            $realTableName = $this->tableBaseName;
            if ($this->tablePartitionType == 4) {
                $realTableName = $this->tableBaseName . '_' . date('Y_m_d');
            } elseif ($this->tablePartitionType == 5) {
                $realTableName = $this->tableBaseName . '_' . date('Y_W');
            } elseif ($this->tablePartitionType == 6) {
                $realTableName = $this->tableBaseName . '_' . date('Y_m');
            }
            if ($this->tablePrefix) {
                $realTableName = $this->tablePrefix . $realTableName;
            }
            $tableNames[] = $quote . $realTableName . $quote;
        }
        return $tableNames;
    }

    /**
     * @param mixed $partitionValue
     * @param string $quote
     * @return string
     */
    public function getDbName($partitionValue = false, $quote = '')
    {
        $dbName = $this->dbBaseName;
        if ($this->dbMaxNum > 1) {
            if ($this->dbPartitionType == 1 || $this->dbPartitionType == 3) {
                // 只分库或者分库分表
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
        return $quote . $dbName . $quote;
    }

    /**
     * @param string $quote
     * @return array
     */
    public function getDbNameRange($quote = '')
    {
        $dbNames = [];
        if ($this->dbMaxNum > 1) {
            if ($this->dbPartitionType == 1 || $this->dbPartitionType == 3) {
                $range = range(1, $this->dbMaxNum);
                foreach ($range as $idx) {
                    $realDbName = $this->dbBaseName . '_' . $idx;
                    if ($this->dbPrefix) {
                        $realDbName = $this->dbPrefix . $realDbName;
                    }
                    $dbNames[] = $quote . $realDbName . $quote;
                }
            }
        } else {
            $realDbName = $this->dbBaseName;
            if ($this->dbPrefix) {
                $realDbName = $this->dbPrefix . $realDbName;
            }
            $dbNames[] = $quote . $realDbName . $quote;
        }
        return $dbNames;
    }

    /**
     * @param array $partition
     * @param string $quote
     * @return string
     */
    public function getDbTableName($partition = [], $quote = '')
    {
        $dbPartitionValue = $partition[$this->dbPartitionField] ?? false;
        $dbName = $this->getDbName($dbPartitionValue, $quote);
        $tablePartitionValue = $partition[$this->tablePartitionField] ?? false;
        $tableName = $this->getTableName($tablePartitionValue, $quote);
        return $dbName . '.' . $tableName;
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

    /**
     * create tables
     * @param bool $drop
     * @return string
     */
    public function create($drop = false)
    {
        if (!$this->createSQL) {
            return;
        }
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
        return implode(PHP_EOL, $sql);
    }

    /**
     * re-create table
     */
    public function recreate()
    {
        $this->create(true);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function supportTransaction()
    {
        if (!$this->engine) {
            throw new \Exception("Table " . get_class($this) . " should specify the \$engine property");
        }
        $engine = strtolower($this->engine);
        return in_array($engine, [
            'innodb',
        ]);
    }

    /**
     * @param Model $model
     * @return array
     */
    public function getModelFields(Model $model)
    {
        $fields = $this->getTableFields();
        $return = [];
        foreach ($fields as $field => $defaultValue) {
            if (($property = $model->hasProperty($field)) != false) {
                $return[$field] = $model->{$property};
            }
        }
        return $return;
    }

    /**
     * @return string
     */
    public function getDbPartitionField()
    {
        return $this->dbPartitionField;
    }

    /**
     * @return int
     */
    public function getDbPartitionType()
    {
        return $this->dbPartitionType;
    }

    /**
     * @return string
     */
    public function getTablePartitionField()
    {
        return $this->tablePartitionField;
    }

    /**
     * @return int
     */
    public function getTablePartitionType()
    {
        return $this->tablePartitionType;
    }

    /**
     * field => default value
     * @return array
     */
    abstract public function getTableFields();
}