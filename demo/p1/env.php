<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/5/31
 * Time: 上午11:53
 */


use Pimple\Container;

$container = new Container();
app(ENV_DEMO_P1, $container);


$container['twig.loader.filesystem_loader.path'] = __DIR__ . '/twig_tpl';
$container['twig.loader.filesystem_loader.namespace'] = 'demo_p1';




/** @var Container $defaultContainer */
$defaultContainer = container(ENV_DEFAULT);
/**
 * 添加路径
 */
$defaultContainer->extend('twig.loader.filesystem_loader', function (\Twig_Loader_Filesystem $loader, Container $c) use ($container) {
    $loader->addPath($container['twig.loader.filesystem_loader.path'], $container['twig.loader.filesystem_loader.namespace']);
    return $loader;
});





















