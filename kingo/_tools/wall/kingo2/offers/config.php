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
define('OFFERS_MYSQL_DB_OFFERS', 'kingo');


/**
 * 跨域
 */
define('OFFERS_ADMIN_ALLOW_ORIGIN', 'http://cp.k8abc.com');

/**
 * 其他配置
 */
define('OFFERS_ADMIN_APP_NAME', 'OFFER 总库');
define('OFFERS_ADMIN_STATIC_BASE_URL', 'http://cp.k8abc.com/offers/admin/public/static');
define('OFFERS_APP_REQUEST_BASE_URL', 'http://cp.k8abc.com/offers/admin');
define('OFFERS_AUTH_REQUEST_BASE_URL', 'http://cp.k8abc.com/users/api');
define('OFFERS_ADMIN_STATIC_VERSION', 2);


define('OFFERS_ADMIN_SESSION_COOKIE_LIFETIME', 24 * 60 * 60);
define('OFFERS_ADMIN_SESSION_COOKIE_PATH', '/');
define('OFFERS_ADMIN_SESSION_COOKIE_DOMAIN', 'cp.k8abc.com');
define('OFFERS_ADMIN_SESSION_COOKIE_SECURE', false);
define('OFFERS_ADMIN_SESSION_COOKIE_HTTP_ONLY', true);


define('OFFERS_SOURCE_MOBI_SUMMER', 'mobi_summer');
define('OFFERS_SOURCE_PUB_NATIVE', 'pub_native');
define('OFFERS_SOURCE_SOLO', 'solo');
define('OFFERS_SOURCE_MOBI_SMARTER', 'mobi_smarter');
define('OFFERS_SOURCE_INPLAYABLE', 'inplayable');


/**
 * 错误码
 */


