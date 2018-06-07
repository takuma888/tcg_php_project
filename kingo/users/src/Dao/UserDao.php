<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午4:44
 */

namespace Users\Dao;


use TCG\MySQL\Query;

class UserDao
{

    public function getUserByUsername($username)
    {
        $sql = <<<SQL
SELECT * FROM {@user_auth} WHERE `username` = :username
SQL;
        $query = new Query($sql);
        $query->setParameter(':username', $username);

    }
}