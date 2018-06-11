<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午4:44
 */

namespace Users\MySQL\Service;


use Users\MySQL\Table\UserAuthTable;

class UserService
{

    /**
     * @param $conditionExpr
     * @param array $params
     * @param array $partitions
     * @return array
     * @throws \Exception
     */
    public function authSelectMany($conditionExpr, array $params = [], array $partitions = [])
    {
        $query = query("SELECT SQL_CALC_FOUND_ROWS * FROM {@table} WHERE " . $conditionExpr);
        $query->table('{@table}', $this->getAuthTable())
            ->setParameters($params);
        $connection = $query->getConnectionForRead();
        $sql = $query->getSQLForRead($partitions);

        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        $stmt = $connection->prepare("SELECT FOUND_ROWS()");
        $stmt->execute();
        $total = $stmt->fetch()['FOUND_ROWS()'];

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * @param $conditionExpr
     * @param array $params
     * @param array $partitions
     * @return array
     * @throws \Exception
     */
    public function authSelectOne($conditionExpr, array $params = [], array $partitions = [])
    {
        $query = query("SELECT * FROM {@table} WHERE " . $conditionExpr);
        $query->table('{@table}', $this->getAuthTable())
            ->setParameters($params);
        $connection = $query->getConnectionForRead();
        $sql = $query->getSQLForRead($partitions);

        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        return [
            'data' => $data,
        ];
    }

    /**
     * @param array $fields
     * @param array $partitions
     * @return int
     * @throws \Exception
     */
    public function authInsertOne(array $fields, array $partitions = [])
    {
        $f = [];
        $v = [];
        $p = [];
        foreach ($fields as $field => $value) {
            $field = trim($field, '`');
            $f[] = "`{$field}`";
            $v[] = ":{$field}";
            $p[":{$field}"] = $value;
        }
        $query = query("INSERT INTO {@table} (" . implode(', ', $f) . ") VALUES (" . implode(', ', $v) . ")");
        $query->table('{@table}', $this->getAuthTable())
            ->setParameters($p);

        $connection = $query->getConnectionForWrite();
        $sql = $query->getSQLForWrite($partitions);

        $stmt = $connection->prepare($sql);
        $stmt->execute($query->getParameters());

        return $connection->lastInsertId();
    }



    public function authInsertMany(array $data, array $partitions = [])
    {

    }


    /**
     * @return UserAuthTable
     */
    private function getAuthTable()
    {
        return table('user_auth');
    }

}