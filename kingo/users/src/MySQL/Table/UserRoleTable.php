<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午3:31
 */

namespace Users\MySQL\Table;

use TCG\MySQL\Table;

class UserRoleTable extends Table
{
    protected $tableBaseName = 'user_role';

    protected $engine = 'InnoDB';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `uid` INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `rid` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '角色组ID',
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  PRIMARY KEY (`uid`, `rid`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;

    public function getTableFields()
    {
        return [
            'uid' => 0,
            'rid' => '',
            'create_at' => 0,
        ];
    }
}