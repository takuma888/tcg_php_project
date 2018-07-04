<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午1:28
 */

/**
 * users数据库连接字符串
 */
define('USERS_MYSQL_DSN_MASTER', 'mysql://root:123456789@mysql:3306/?charset=utf8mb4&collate=utf8mb4_general_ci&timezone=+8:00');
/**
 * users数据库基础名称
 */
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
 * 跨域
 */
define('USERS_ADMIN_ALLOW_ORIGIN', 'http://localhost:8080');
define('USERS_API_ALLOW_ORIGIN', 'http://localhost:8080');

/**
 * 错误码
 */


