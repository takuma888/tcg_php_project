<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/23
 * Time: 下午4:06
 */

namespace Offers\MySQL\Table;


use TCG\MySQL\Table;

class OfferBaseTable extends Table
{
    protected $tableBaseName = 'offer_base';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '唯一ID',
  `source` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '来源',
  `offer_id` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '来源的offer的ID',
  `offer_name` VARCHAR(256) NOT NULL DEFAULT '' COMMENT 'offer的产品的名称',
  `package_name` VARCHAR(256) NOT NULL DEFAULT '' COMMENT 'offer的包名',
  `country` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '国家',
  `platform` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '平台，操作系统如ios或者android',
  `payout_type` VARCHAR(8) NOT NULL DEFAULT '' COMMENT '支付的单位',
  `payout` DECIMAL(14, 6) NOT NULL DEFAULT 0.000000 COMMENT '支付的值',
  `category` VARCHAR(256) NOT NULL DEFAULT '' COMMENT 'offer的分类',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'offer的状态',
  PRIMARY KEY (`id`, `country`),
  KEY `country` (`country`),
  KEY `platform` (`platform`),
  KEY `status` (`status`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci PARTITION BY KEY(`country`);
SQL;

    protected $engine = 'InnoDB';

    public function getTableFields()
    {
        return [
            'id' => '',
            'source' => '',
            'offer_id' => '',
            'offer_name' => '',
            'package_name' => '',
            'country' => '',
            'platform' => '',
            'payout_type' => '',
            'payout' => 0,
            'category' => '',
            'status' => 0,
        ];
    }

}