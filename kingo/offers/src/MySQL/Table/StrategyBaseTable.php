<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/30
 * Time: 下午12:10
 */


namespace Offers\MySQL\Table;


use TCG\MySQL\Table;

class StrategyBaseTable extends Table
{

    protected $tableBaseName = 'strategy_base';

    protected $engine = 'InnoDB';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `priority` SMALLINT(5) NOT NULL DEFAULT 0 COMMENT '权重、优先级',
  `name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `client` VARCHAR(32) DEFAULT NULL COMMENT '客户端渠道',
  `start_at` TIMESTAMP NULL DEFAULT NULL COMMENT '起始时间戳，空表示不限制',
  `end_at` TIMESTAMP NULL DEFAULT NULL COMMENT '结束时间戳，空表示不限制',
  `geo2` CHAR(2) NULL DEFAULT NULL COMMENT '国家，空表示全球', 
  `geo3` CHAR(3) NULL DEFAULT NULL COMMENT '国家，空表示全球', 
  `description` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '描述',
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  `update_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;

    public function getTableFields()
    {
        return [
            'id' => 0,
            'priority' => 0,
            'name' => '',
            'client' => null,
            'start_at' => null,
            'end_at' => null,
            'geo2' => null,
            'geo3' => null,
            'description' => '',
            'create_at' => '',
            'update_at' => '',
        ];
    }

}