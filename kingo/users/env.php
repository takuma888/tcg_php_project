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

$container = container(ENV_USERS);
if (!$container) {
    $container = new Container();
    app(ENV_USERS, $container);
}

// http environment
$container['http.environment'] = function () {
    return Environment::mock($_SERVER);
};
// http request
$container['http.request'] = function (Container $c) {
    return Request::createFromEnvironment($c['http.environment']);
};

// http response
$container['http.response'] = $container->factory(function () {
    return new Response();
});


