<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/30
 * Time: 下午12:11
 */

namespace Offers\MySQL\Table;


use TCG\MySQL\Table;

class StrategyExtTable extends Table
{

    protected $tableBaseName = 'strategy_ext';

    protected $engine = 'InnoDB';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `strategy_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '策略ID',
  `category` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '分类',
  `type` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '逻辑类型',
  `value` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '逻辑值',
  PRIMARY KEY (`id`),
  KEY `strategy` (`category`, `type`, `value`(100))
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;

    public function getTableFields()
    {
        return [
            'id' => 0,
            'strategy_id' => 0,
            'category' => '',
            'type' => 0,
            'value' => '',
        ];
    }

}