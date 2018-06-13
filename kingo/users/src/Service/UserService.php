<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午4:44
 */

namespace Users\Service;


use Users\MySQL\Table\UserAuthTable;

class UserService
{


    /**
     * @param $clauseExpr
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function authSelectMany($clauseExpr, array $params = [])
    {
        $clauseExpr = trim($clauseExpr);
        if (!$clauseExpr) {
            $clauseExpr = 'WHERE 1';
        }
        if (strtoupper(substr($clauseExpr, 0, 6)) !== 'WHERE') {
            $clauseExpr = 'WHERE ' . $clauseExpr;
        }
        $query = query("SELECT SQL_CALC_FOUND_ROWS * FROM {@table} " . $clauseExpr);
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
     * @param $clauseExpr
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function authSelectOne($clauseExpr, array $params = [])
    {
        $clauseExpr = trim($clauseExpr);
        if (!$clauseExpr) {
            $clauseExpr = 'WHERE 1';
        }
        if (strtoupper(substr($clauseExpr, 0, 6)) !== 'WHERE') {
            $clauseExpr = 'WHERE ' . $clauseExpr;
        }
        $query = query("SELECT * FROM {@table} " . $clauseExpr);
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
     * @return UserAuthTable
     */
    private function getAuthTable()
    {
        return table('user_auth');
    }

}