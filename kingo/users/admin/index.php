<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/2
 * Time: 下午5:23
 */
require __DIR__ . '/../../bootstrap.php';

use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use FastRoute\RouteParser\Std;
use FastRoute\RouteCollector;

/**
 * 检查依赖的服务
 */

/**
 * 注册私有服务
 */
require __DIR__ . '/../private.php';

/**
 * 注册容器到环境中
 */
$container = container(ENV_USERS);
if (!$container) {
    $container = new Container();
    env(ENV_USERS, $container);
}

// 路由集合
$container['route.collector'] = function () {
    return new RouteCollector(new Std(), new \FastRoute\DataGenerator\GroupCountBased());
};



// 路由分发器
$container['route.dispatcher'] = $container->factory(function (Container $c) {
    return new \FastRoute\Dispatcher\GroupCountBased($c['route.collector']->getData());
});

/**
 * 设置当前app
 */
app(ENV_USERS);

/** @var \TCG\Middleware\Dispatcher $dispatcher */
$dispatcher = env()->get('middleware.dispatcher');
/**
 * 加载session中间件
 */
$dispatcher->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
    env()->get('session')->start();
    return $next->handle($request);
});
/**
 * 加载路由中间件
 */
$dispatcher->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
    $response = $next->handle($request);
    $pathInfo = $request->getServerParams()['PATH_INFO'];
    $pathInfo = '/' . trim($pathInfo, '/');
    $root = __DIR__ . '/backend';
    $items = explode('/', trim($pathInfo, '/'));
    $itemCount = count($items);
    $count = 0;
    while ($count <= $itemCount) {
        $path = '/' . implode('/', array_slice($items, $count, $itemCount - $count));
        $base = implode('/', array_slice($items, 0, $count));
        $pattern = $root . '/' . $base;
        $file = null;
        if (is_file($pattern . '.php')) {
            $file = $pattern . '.php';
        } elseif (is_dir($pattern)) {
            $file = rtrim($pattern, '/') . '/index.php';
        }
        if ($file && file_exists($file)) {
            $cwd = getcwd();
            chdir(dirname($file));
            require $file;
            chdir($cwd);
            // 进行路由分发
            $routeInfo = env()->get('route.dispatcher')
                ->dispatch($request->getMethod(), $path);
            switch ($routeInfo[0]) {
                case FastRoute\Dispatcher::NOT_FOUND:
                    // ... 404 Not Found
                    break;
                case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    $allowedMethods = $routeInfo[1];
                    // ... 405 Method Not Allowed
                    break;
                case FastRoute\Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    $response = call_user_func_array($handler, [$request, env()->get('http.response')] + $vars);
                    return $response;
                    break;
            }
        }
        $count += 1;
    }
    // 再分发一遍
    $routeInfo = env()->get('route.dispatcher')
        ->dispatch($request->getMethod(), $pathInfo);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            // ... 404 Not Found
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            $response = call_user_func_array($handler, [$request, env()->get('http.response')] + $vars);
            return $response;
            break;
    }
    return $response;
});

/**
 * route /
 */
route()->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    return redirect($response, 'http://admin.users.kingo.com');
});
// tests
route()->get('/logging/{level}[/{message}]', function (ServerRequestInterface $request, ResponseInterface $response, $level, $message = null) {
    logger()->log($level, 'a ' . $level . ' log message: ' . $message);
    $response->getBody()
        ->write('<h1>' . $level . '</h1>');
    return $response;
});
route()->get('/test', function (ServerRequestInterface $request, ResponseInterface $response) {
    table('user_auth')->recreate();
    table('session')->recreate();
    return $response;
});
route()->get('/echo[/{content}]', function (ServerRequestInterface $request, ResponseInterface $response, $content = null) {
    $last = session()->get('echo');
    session()->set('echo', $content);
    $response->getBody()
        ->write("<h1>Echo!Last is {$last} but the new is {$content}</h1>");
    return $response;
});


/** @var ResponseInterface $response */
$response = $dispatcher->dispatch(env()->get('http.request'));
env()->get('http.response_sender')->send($response);
