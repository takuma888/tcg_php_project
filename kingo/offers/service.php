<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 上午9:49
 */

use Pimple\Container;

/**
 * 注册容器到环境中
 */
$container = container(ENV_OFFERS);
if (!$container) {
    $container = new Container();
    env(ENV_OFFERS, $container);
}

/**
 * 加载配置
 */
require __DIR__ . '/config.php';

/**
 * mysql数据库连接dsn
 */
$container['offers.mysql.servers.master_dsn'] = function () {
    return OFFERS_MYSQL_DSN_MASTER;
};
$container['offers.mysql.servers.slave_dsn'] = function(Container $c) {
    return $c['offers.mysql.servers.master_dsn'];
};
/**
 * 库名称
 */
$container['offers.mysql.database.offers'] = $container->factory(function () {
    return OFFERS_MYSQL_DB_OFFERS;
});
/**
 * mysql数据库服务器
 * @param Container $c
 * @return \TCG\MySQL\Server
 */
$container['offers.mysql.servers.master'] = function (Container $c) {
    return new \TCG\MySQL\Server($c['offers.mysql.servers.master_dsn']);
};
/**
 * mysql数据库服务器
 * @param Container $c
 * @return \TCG\MySQL\Server
 */
$container['offers.mysql.servers.slave'] = function (Container $c) {
    return new \TCG\MySQL\Server($c['offers.mysql.servers.slave_dsn']);
};
/**
 * offer 基础表
 * @param Container $c
 * @return \Offers\MySQL\Table\OfferBaseTable
 */
$container['offers.mysql.tables.offer_base'] = function (Container $c) {
    return new Offers\MySQL\Table\OfferBaseTable($c['offers.mysql.servers.master'], [], $c['offers.mysql.database.offers']);
};
/**
 * @param Container $c
 * @return \Offers\MySQL\Table\OfferExtTable
 */
$container['offers.mysql.tables.offer_ext'] = function (Container $c) {
    return new Offers\MySQL\Table\OfferExtTable($c['offers.mysql.servers.master'], [], $c['offers.mysql.database.offers']);
};