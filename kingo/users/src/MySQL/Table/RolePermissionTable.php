<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午3:25
 */

namespace Users\MySQL\Table;

use TCG\MySQL\Table;

class RolePermissionTable extends Table
{
    protected $tableBaseName = 'role_permission';

    protected $engine = 'InnoDB';

    protected $createSQL = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '主键,角色组的英文名',
  `name` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '名称',
  `description` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '描述',
  `priority` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优先级',
  `permissions` TEXT DEFAULT NULL COMMENT '权限,2的n次幂的2进制表示',
  `create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  `update_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;

    public function getTableFields()
    {
        return [
            'id' => '',
            'name' => '',
            'description' => '',
            'priority' => 0,
            'permissions' => '0',
            'create_at' => 0,
            'update_at' => 0,
        ];
    }
}