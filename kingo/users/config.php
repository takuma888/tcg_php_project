<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午1:28
 */


define('USERS_MYSQL_DSN_MASTER', 'mysql://root:123456789@mysql:3306/?charset=utf8mb4&collate=utf8mb4_general_ci&timezone=+8:00');

define('USERS_MYSQL_DB_USERS', 'users');

/**
 * 角色
 */
define('ROLE_ROOT', 'root');
define('ROLE_SUPERADMIN', 'superadmin');
define('ROLE_DEVELOPER', 'developer');

/**
 * 权限
 */
define('PERMISSION_ALL', '0b' . str_repeat('1', 9999));

/**
 * 错误码
 */


