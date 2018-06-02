<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 上午9:54
 */

require __DIR__ . '/../../../bootstrap.php';

// 设置当前app
app(ENV_USERS);

/** @var \TCG\Middleware\Dispatcher $app */
$dispatcher = env()->get('middleware.dispatcher');
// 加载中间件
$dispatcher->add(env()->get('middleware.error_handler'));
// 加载路由配置




/** @var \TCG\Http\Response $response */
$response = $dispatcher->dispatch(env()->get('http.request'), env()->get('http.response'));
$response->send();
//pre($_SERVER['PATH_INFO']);
