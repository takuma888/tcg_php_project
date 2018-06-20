<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午2:28
 */

namespace Users\MySQL\Table;

use TCG\MySQL\Table;

class UserAuthTable extends Table
{
    protected $tableBaseName = 'user_auth';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(128) DEFAULT NULL COMMENT '用户名',
  `email` VARCHAR(128) DEFAULT NULL COMMENT '邮箱',
  `mobile` VARCHAR(16) DEFAULT NULL COMMENT '手机',
  `password` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '密码',
  `session_id` VARBINARY(128) NOT NULL DEFAULT '' COMMENT 'SESSION ID',
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  `register_at` TIMESTAMP NULL COMMENT '注册时间戳',
  `login_at` TIMESTAMP NULL COMMENT '登录时间戳',
  `update_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`),
  KEY `session_id` (`session_id`),
  PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;

    public function getTableFields()
    {
        return [
            'id' => 0,
            'username' => null,
            'email' => null,
            'mobile' => null,
            'password' => '',
            'session_id' => '',
            'create_at' => '',
            'register_at' => '',
            'login_at' => '',
            'update_at' => '',
        ];
    }


    protected $engine = 'innodb';
}