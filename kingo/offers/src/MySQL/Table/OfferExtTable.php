<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/23
 * Time: 下午4:09
 */

namespace Offers\MySQL\Table;

use TCG\MySQL\Table;

class OfferExtTable extends Table
{
    protected $tableBaseName = 'offer_ext';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '唯一ID',
  `info` MEDIUMTEXT NOT NULL COMMENT 'offer的具体内容',
  PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;

    protected $engine = 'InnoDB';

    public function getTableFields()
    {
        return [
            'id' => '',
            'info' => [],
        ];
    }
}