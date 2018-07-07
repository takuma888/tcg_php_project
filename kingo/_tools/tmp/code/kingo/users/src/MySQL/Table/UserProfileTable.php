<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午3:13
 */

namespace Users\MySQL\Table;


use TCG\MySQL\Table;

class UserProfileTable extends Table
{
    protected $tableBaseName = 'user_profile';

    protected $engine = 'InnoDB';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL COMMENT '主键',
  `nickname` VARCHAR(128) DEFAULT '' COMMENT '昵称',
  `qq` VARCHAR(16) DEFAULT '' COMMENT 'QQ号码',
  `wei_xin` VARCHAR(32) DEFAULT '' COMMENT '微信号',
  `first_name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '名字',
  `middle_name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '中间名',
  `last_name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '姓',
  `birthday` TIMESTAMP NULL COMMENT '生日',
  `language` VARCHAR(8) NOT NULL DEFAULT '' COMMENT '语言',
  `country` VARCHAR(3) NOT NULL DEFAULT '' COMMENT '国家,ISO编码',
  `province` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '省份/州',
  `city` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '城市',
  `address` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '地址',
  `avatar` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '头像',
  `update_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;

    public function getTableFields()
    {
        return [
            'id' => 0,
            'nickname' => '',
            'qq' => '',
            'wei_xin' => '',
            'first_name' => '',
            'middle_name' => '',
            'last_name' => '',
            'birthday' => '',
            'language' => '',
            'country' => '',
            'province' => '',
            'city' => '',
            'address' => '',
            'avatar' => '',
            'update_at' => '',
        ];
    }
}