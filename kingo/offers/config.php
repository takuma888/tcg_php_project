<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/23
 * Time: 下午4:11
 */

/**
 * users数据库连接字符串
 */
define('OFFERS_MYSQL_DSN_MASTER', 'mysql://root:123456789@mysql:3306/?charset=utf8mb4&collate=utf8mb4_general_ci&timezone=+8:00');
/**
 * users数据库基础名称
 */
define('OFFERS_MYSQL_DB_OFFERS', 'offers');


/**
 * 跨域
 */
define('OFFERS_ADMIN_ALLOW_ORIGIN', 'http://localhost:8080');

/**
 * 其他配置
 */
define('OFFERS_ADMIN_APP_NAME', 'OFFER 总库');

define('OFFERS_ADMIN_PUBLIC_BASE_URL', 'http://tcg.php.localhost.com/kingo/offers/admin/react/build');

define('OFFERS_APP_REQUEST_BASE_URL', 'http://tcg.php.localhost.com/kingo/offers/admin');

define('OFFERS_AUTH_REQUEST_BASE_URL', 'http://tcg.php.localhost.com/kingo/users/api');

define('OFFERS_ADMIN_SESSION_COOKIE_LIFETIME', 24 * 60 * 60);
define('OFFERS_ADMIN_SESSION_COOKIE_PATH', '/');
define('OFFERS_ADMIN_SESSION_COOKIE_DOMAIN', 'tcg.php.localhost.com');
define('OFFERS_ADMIN_SESSION_COOKIE_SECURE', false);
define('OFFERS_ADMIN_SESSION_COOKIE_HTTP_ONLY', true);

/**
 * 错误码
 */


