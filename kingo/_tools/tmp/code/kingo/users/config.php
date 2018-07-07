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
define('USERS_ADMIN_ALLOW_ORIGIN', 'http://cp.k8abc.com');
define('USERS_API_ALLOW_ORIGIN', 'http://cp.k8abc.com');

/**
 * 其他配置
 */

define('USERS_ADMIN_APP_NAME', '用户管理系统');

define('USERS_ADMIN_STATIC_BASE_URL', 'http://cp.k8abc.com/users/admin/public/static');

define('USERS_ADMIN_REQUEST_BASE_URL', 'http://cp.k8abc.com/users/admin');

define('USERS_ADMIN_SESSION_COOKIE_LIFETIME', 24 * 60 * 60);
define('USERS_ADMIN_SESSION_COOKIE_PATH', '/');
define('USERS_ADMIN_SESSION_COOKIE_DOMAIN', 'cp.k8abc.com');
define('USERS_ADMIN_SESSION_COOKIE_SECURE', false);
define('USERS_ADMIN_SESSION_COOKIE_HTTP_ONLY', true);

/**
 * 错误码
 */


