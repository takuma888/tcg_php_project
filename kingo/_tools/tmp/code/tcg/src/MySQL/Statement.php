<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午12:33
 */

namespace TCG\MySQL;

use PDOStatement;

class Statement extends PDOStatement
{

    /**
     * @var Connection
     */
    public $connection;

    /**
     * Statement constructor.
     * @param Connection $connection
     */
    protected function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
}