<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/6
 * Time: 下午1:14
 */

namespace TCG\MySQL;

class Query
{

    const PARSE_READ = 'read';

    const PARSE_WRITE = 'write';


    /**
     * @var string
     */
    protected $sql = '';
    /**
     * @var Table[]
     */
    protected $tables = [];
    /**
     * The query parameters.
     *
     * @var array
     */
    private $params = array();
    /**
     * The parameter type map of this query.
     *
     * @var array
     */
    private $paramTypes = array();

    /**
     * @var bool
     */
    private $parsed = false;

    /**
     * Query constructor.
     * @param string $sql
     */
    public function __construct($sql = '')
    {
        $this->sql = $sql;
    }


    /**
     * @param string $sql
     * @return $this
     */
    public function sql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * @param $placeholder
     * @param Table $table
     * @return $this
     */
    public function table($placeholder, Table $table)
    {
        if (!isset($this->tables[$placeholder])) {
            $this->tables[$placeholder] = $table;
        }
        return $this;
    }


    /**
     * @param $key
     * @param $value
     * @param null $type
     * @return $this
     */
    public function setParameter($key, $value, $type = null)
    {
        if ($type !== null) {
            $this->paramTypes[$key] = $type;
        }
        $this->params[$key] = $value;
        return $this;
    }


    /**
     * @param array $params
     * @param array $types
     * @return $this
     */
    public function setParameters(array $params, array $types = [])
    {
        $this->paramTypes = $types;
        $this->params = $params;
        return $this;
    }

    /**
     * Gets all defined query parameters for the query being constructed indexed by parameter index or name.
     *
     * @return array The currently defined query parameters indexed by parameter index or name.
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Gets a (previously set) query parameter of the query being constructed.
     *
     * @param mixed $key The key (index or name) of the bound parameter.
     *
     * @return mixed The value of the bound parameter.
     */
    public function getParameter($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * Gets all defined query parameter types for the query being constructed indexed by parameter index or name.
     *
     * @return array The currently defined query parameter types indexed by parameter index or name.
     */
    public function getParameterTypes()
    {
        return $this->paramTypes;
    }

    /**
     * Gets a (previously set) query parameter type of the query being constructed.
     *
     * @param mixed $key The key (index or name) of the bound parameter type.
     *
     * @return mixed The value of the bound parameter type.
     */
    public function getParameterType($key)
    {
        return isset($this->paramTypes[$key]) ? $this->paramTypes[$key] : null;
    }


    /**
     * @param array $partitions
     * @return string
     * @throws \Exception
     */
    public function getSQLForRead(array $partitions = [])
    {
        $serverIds = [];
        $dbTableNames = [];
        foreach ($this->tables as $placeholder => $table) {
            if (!isset($serverIds[$placeholder])) {
                $serverIds[$placeholder] = [];
            }
            foreach ($table->getReadServers() as $server) {
                $serverIds[$placeholder][] = $server->getId();
            }
            $tablePartition = isset($partitions[$placeholder]) ? $partitions[$placeholder] : [];
            $dbTableName = $table->getDbTableName($tablePartition, '`');
            $dbTableNames[$placeholder] = $dbTableName;
        }
        $serverIds = array_values($serverIds);
        $intersectServerIds = call_user_func_array('array_intersect', $serverIds);
        if (!$intersectServerIds) {
            throw new \Exception("一条SQL语句中不能进行跨服务器的读取");
        }
        if ($dbTableNames) {
            $this->sql = strtr($this->sql, $dbTableNames);
        }
        $this->parsed = self::PARSE_READ;
        return $this->sql;
    }

    /**
     * @param array $partitions
     * @return string
     * @throws \Exception
     */
    public function getSQLForWrite(array $partitions = [])
    {
        $serverId = null;
        $dbTableNames = [];
        foreach ($this->tables as $placeholder => $table) {
            $server = $table->getWriteServer();
            if (!$serverId) {
                $serverId = $server->getId();
            }
            if ($serverId != $server->getId()) {
                throw new \Exception("一条SQL语句中不能进行跨服务器的写入");
            }
            $tablePartition = isset($partitions[$placeholder]) ? $partitions[$placeholder] : [];
            $dbTableName = $table->getDbTableName($tablePartition, '`');
            $dbTableNames[$placeholder] = $dbTableName;
        }
        if ($dbTableNames) {
            $this->sql = strtr($this->sql, $dbTableNames);
        }
        $this->parsed = self::PARSE_WRITE;
        return $this->sql;
    }

    /**
     * @return Connection
     * @throws \Exception
     */
    public function getConnectionForWrite()
    {
        /** @var Server[] $servers */
        $servers = [];
        $connections = [];
        foreach ($this->tables as $placeholder => $table) {
            $server = $table->getWriteServer();
            if (!isset($connections[$placeholder])) {
                $connections[$placeholder] = [];
            }
            $servers[$server->getId()] = $server;
            $connections[$placeholder][] = $server->getId();
        }
        $connections = array_values($connections);
        $connections = call_user_func_array('array_intersect', $connections);
        if ($connections) {
            $connections = array_values($connections);
            $serverId = $connections[0];
            return $servers[$serverId]->connect();
        } else {
            throw new \Exception("Can not found mysql connection");
        }
    }

    /**
     * @return Connection
     * @throws \Exception
     */
    public function getConnectionForRead()
    {
        /** @var Server[] $servers */
        $servers = [];
        $connections = [];
        foreach ($this->tables as $placeholder => $table) {
            if (!isset($connections[$placeholder])) {
                $connections[$placeholder] = [];
            }
            foreach ($table->getReadServers() as $server) {
                $servers[$server->getId()] = $server;
                $connections[$placeholder][] = $server->getId();
            }
        }
        $connections = array_values($connections);
        $connections = call_user_func_array('array_intersect', $connections);
        if ($connections) {
            $connections = array_values($connections);
            $serverId = $connections[0];
            return $servers[$serverId]->connect();
        } else {
            throw new \Exception("Can not found mysql connection");
        }
    }

}