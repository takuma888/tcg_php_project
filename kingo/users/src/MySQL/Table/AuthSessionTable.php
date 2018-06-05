<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午6:38
 */

namespace Users\MySQL\Table;


use TCG\MySQL\Table;

class AuthSessionTable extends Table
{
    protected $tableBaseName = 'auth_session';

    protected $createSQL = <<<SQL

SQL;

    public function getTableFields()
    {
        return [

        ];
    }
}