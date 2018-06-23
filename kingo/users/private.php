<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午2:12
 */

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