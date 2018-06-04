<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 上午9:54
 */

use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../../../bootstrap.php';
require __DIR__ . '/../private.php';

// 设置当前app
app(ENV_USERS);

/** @var \TCG\Middleware\Dispatcher $app */
$dispatcher = env()->get('middleware.dispatcher');
// 加载中间件
$dispatcher->add(function (ServerRequestInterface $request, $next) {

});


/** @var \TCG\Http\Response $response */
$response = $dispatcher->dispatch(env()->get('http.request'), env()->get('http.response'));
$response->send();
//pre($_SERVER['PATH_INFO']);
