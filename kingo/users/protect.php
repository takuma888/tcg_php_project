<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 上午9:50
 */

use Pimple\Container;
use TCG\Http\Environment;
use TCG\Http\Request;
use TCG\Http\Response;
use TCG\Middleware\Dispatcher;

/**
 * 注册容器到环境中
 */
$container = container(ENV_USERS);
if (!$container) {
    $container = new Container();
    env(ENV_USERS, $container);
}


// tcg http environment
$container['tcg.http.environment'] = function () {
    return Environment::mock($_SERVER);
};
// tcg http request
$container['http.request'] = function (Container $c) {
    return Request::createFromEnvironment($c['tcg.http.environment']);
};

// tcg http response
$container['http.response'] = $container->factory(function () {
    return new Response();
});

// 中间件
// dispatcher
$container['middleware.dispatcher'] = $container->factory(function () {
    return new Dispatcher();
});

