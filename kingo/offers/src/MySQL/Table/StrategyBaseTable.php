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
  `priority` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '权重、优先级',
  `name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `description` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '描述',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;

    public function getTableFields()
    {
        return [
            'id' => 0,
            'priority' => 0,
            'name' => '',
            'description' => '',
            'create_at' => '',
            'update_at' => '',
        ];
    }

}