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
     * @return array
     * @throws \Exception
     */
    public function authSelectMany($conditionExpr, array $params = [])
    {
        if (!$conditionExpr) {
            $conditionExpr = '1';
        }
        $query = query("SELECT SQL_CALC_FOUND_ROWS * FROM {@table} WHERE " . $conditionExpr);
        $query->table('{@table}', $this->getAuthTable())
            ->setParameters($params);
        $connection = $query->getConnectionForRead();
        $sql = $query->getSQLForRead();

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
     * @return array
     * @throws \Exception
     */
    public function authSelectOne($conditionExpr, array $params = [])
    {
        $query = query("SELECT * FROM {@table} WHERE " . $conditionExpr);
        $query->table('{@table}', $this->getAuthTable())
            ->setParameters($params);
        $connection = $query->getConnectionForRead();
        $sql = $query->getSQLForRead();

        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        return [
            'data' => $data,
        ];
    }

    /**
     * @param array $fields
     * @return int
     * @throws \Exception
     */
    public function authInsertOne(array $fields)
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
        $stmt = $connection->prepare($query->getSQLForWrite());
        $stmt->execute($query->getParameters());

        return $connection->lastInsertId();
    }



    public function authInsertMany(array $data)
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