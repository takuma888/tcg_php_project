<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/23
 * Time: 下午4:11
 */

use Pimple\Container;

/**
 * 私有服务
 */

/**
 * 注册容器到环境中
 */
$container = container(ENV_OFFERS);
if (!$container) {
    $container = new Container();
    env(ENV_OFFERS, $container);
}
/**
 * @return \Offers\Utils\Curl
 */
$container['offers.utils.curl'] = function () {
    return new \Offers\Utils\Curl();
};
/**
 * @return \Offers\Utils\Ip2Country
 */
$container['offers.utils.ip2country'] = function () {
    return new \Offers\Utils\Ip2Country();
};
/**
 * @return \Offers\Utils\ISO3166
 */
$container['offers.utils.iso3166'] = function () {
    return new \Offers\Utils\ISO3166();
};
/**
 * @return \Offers\Service\OfferImportService
 */
$container[\Offers\Service\OfferImportService::class] = function () {
    return new \Offers\Service\OfferImportService();
};
/**
 * @return \Offers\Service\OfferExportService
 */
$container[\Offers\Service\OfferExportService::class] = function () {
    return new \Offers\Service\OfferExportService();
};

/**
 * 定义一些辅助方法
 */

/**
 * @return \FastRoute\RouteCollector
 */
function route()
{
    return env()->get('route.collector');
}

/**
 * @param $tableBaseName
 * @return \TCG\MySQL\Table
 */
function table($tableBaseName)
{
    $idPrefix = 'offers.mysql.tables';
    $id = "{$idPrefix}.{$tableBaseName}";
    if (env(ENV_OFFERS)->has($id)) {
        return env(ENV_OFFERS)->get($id);
    }
    throw new \RuntimeException("Table {$id} not found");
}

/**
 * @param string $sql
 * @return \TCG\MySQL\Query
 */
function query($sql = '')
{
    $query = new \TCG\MySQL\Query($sql);
    return $query;
}