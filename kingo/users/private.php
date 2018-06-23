<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午2:12
 */

use Pimple\Container;

/**
 * 扩展
 */
/** @var Container $defaultContainer */
$defaultContainer = container(ENV_DEFAULT);
/**
 * 添加路径
 */
$defaultContainer->extend('twig.loader.filesystem_loader', function (\Twig_Loader_Filesystem $loader, Container $c) use ($container) {
    $loader->addPath(__DIR__ . '/admin/template', 'users:admin');
    return $loader;
});
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
    $idPrefix = 'users.mysql.tables';
    $id = "{$idPrefix}.{$tableBaseName}";
    if (env()->has($id)) {
        return env()->get($id);
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