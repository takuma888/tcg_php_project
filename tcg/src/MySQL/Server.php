<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午12:36
 */

namespace TCG\MySQL;

use PDO;
use PDOException;

class Server
{
    /**
     * @var array
     */
    protected $config = [];
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $dsn;
    /**
     * @var Connection[]
     */
    protected static $connections = [];

    /**
     * Server constructor.
     * @param string $dsn
     */
    public function __construct($dsn)
    {
        $this->dsn = $dsn;
        $dsn = parse_url($this->dsn);
        $config = [
            'driver' => strtolower($dsn['scheme']),
            'host' => $dsn['host'],
        ];
        if (!empty($dsn['port'])) {
            $config['port'] = $dsn['port'];
        } else {
            $config['port'] = 3306;
        }
        $config['user'] = $dsn['user'];
        $config['pass'] = $dsn['pass'];
        $config['charset'] = 'utf8';
        if (isset($dsn['query'])) {
            $option = array();
            parse_str($dsn['query'], $option);
            $config = array_merge($config, $option);
        }
        $this->config = $config;

        $this->id = strtr('{$driver}://{$host}:{$port}', [
            '{$driver}' => $config['driver'],
            '{$host}' => $config['host'],
            '{$port}' => $config['port'],
        ]);
    }

    /**
     * @return string
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Connection
     */
    public function connect()
    {
        $id = $this->getId();
        $config = $this->getConfig();
        if (!isset(self::$connections[$id])) {
            $dsn = $config['driver'] . ':host=' . $config['host'] . ';port=' . $config['port'];
            $user = $config['user'];
            $pass = $config['pass'];
            $connectionOptions = [
//                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
//                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $pdo = new Connection($dsn, $user, $pass, $connectionOptions);

            $pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, ['TCG\MySQL\Statement', [$pdo]]);
            $pdo->exec("set @@sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
            if (!empty($config['collate'])) {
                $pdo->exec('SET NAMES ' . $config['charset'] . ' COLLATE ' . $config['collate']);
            } else {
                $pdo->exec('SET NAMES ' . $config['charset']);
            }
            self::$connections[$id] = $pdo;
        }
        return self::$connections[$id];
    }

    /**
     * @return Connection
     */
    public function reconnect()
    {
        $this->disconnect();
        return $this->connect();
    }

    /**
     * Disconnect the db connection
     */
    public function disconnect()
    {
        $id = $this->getId();
        if (isset(self::$connections[$id])) {
            unset(self::$connections[$id]);
        }
    }

    /**
     * @throws PDOException
     */
    public function ping()
    {
        $id = $this->getId();
        try {
            if (!isset(self::$connections[$id])) {
                throw new PDOException('MySQL server has gone away');
            }
            self::$connections[$id]->getAttribute(Connection::ATTR_SERVER_INFO);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
                throw $e;
            }
        }
    }
}