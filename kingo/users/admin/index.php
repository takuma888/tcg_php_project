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
 * 跨域问题
 */
header("Access-Control-Allow-Origin: " . USERS_ADMIN_ALLOW_ORIGIN);
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: X-Requested-With, accept, content-type, xxxx");

/**
 * 检查依赖的服务
 */
/**
 * 扩展 基础服务
 */
/** @var Container $defaultContainer */
$defaultContainer = container(ENV_DEFAULT);
/**
 * 添加路径
 */
$defaultContainer->extend('twig.loader.filesystem_loader', function (\Twig_Loader_Filesystem $loader, Container $c) use ($container) {
    $loader->addPath(__DIR__ . '/template', 'users:admin');
    return $loader;
});
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
    /** @var ResponseInterface $response */
    $response = $next->handle($request);
    return $response;
});
/**
 * 针对 OPTIONS method 快速返回
 */
$dispatcher->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
    $method = $request->getMethod();
    if (strtoupper($method) == 'OPTIONS') {
        $response = env()->get('http.response');
        return $response;
    }
    return $next->handle($request);
});
/**
 * 接口返回格式化中间件
 */
$dispatcher->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
    if ($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
        $json = [
            'msg' => '',
            'code' => 0,
            'data' => [],
            'flash' => [],
        ];
        // ajax 需要返回json
        try {
            $response = $next->handle($request);
            $string = strval($response->getBody());
            $arr = json_decode($string, true);
            $json['data'] = $arr;
        } catch (\Exception $e) {
            $json['msg'] = $e->getMessage();
            $json['code'] = $e->getCode() ? : \TCG\Http\StatusCode::HTTP_INTERNAL_SERVER_ERROR;
        }
        $flashTypes = [
            'error', 'warning', 'info', 'success'
        ];
        foreach ($flashTypes as $flashType) {
            if (!isset($json['flash'][$flashType])) {
                $json['flash'][$flashType] = [];
            }
            $flashMessages = flash()->get($flashType);
            $json['flash'][$flashType] = $flashMessages;
        }
        $response = env()->get('http.response');
        return json($response, $json, \TCG\Http\StatusCode::HTTP_OK);
    } else {
        return $next->handle($request);
    }
});
/**
 * 登录检测中间件
 */
$dispatcher->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
    $noAuth = $request->getHeaderLine('No-Auth');
    if ($noAuth != '1') {
        $pathInfo = $request->getServerParams()['PATH_INFO'];
        $pathInfo = '/' . trim($pathInfo, '/');
        // path_info 白名单
        $noCheckPathInfo = [
            '/', // 首页
            '/test', // 测试
            '/login', // 登录
            '/logout', // 退出
            '/session', // 获取session
        ];
        if ($noCheckPathInfo) {
            if (!in_array($pathInfo, $noCheckPathInfo)) {
                // 不在白名单中
                if (!session()->get('uid')) {
                    // 没有登录
                    throw new \Exception("请登录");
                }
            }
        }
    }
    return $next->handle($request);
});
/**
 * 加载路由中间件
 */
$dispatcher->add(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
    $response = $next->handle($request);
    $pathInfo = $request->getServerParams()['PATH_INFO'];
    $pathInfo = '/' . trim($pathInfo, '/');
    $root = __DIR__ . '/controller';
    $items = explode('/', trim($pathInfo, '/'));
    $itemCount = count($items);
    $count = 0;
    while ($count <= $itemCount) {
//        $path = '/' . implode('/', array_slice($items, $count, $itemCount - $count));
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
    env()->get('twig')->display('@users:admin/index.html.twig', [
        'app_name' => '用户管理系统',
        'static_version' => time(),
        'static_base_url' => 'http://tcg.php.localhost.com/kingo/users/admin/vue/dist/static',
        'request_base_url' => 'http://tcg.php.localhost.com/kingo/users/admin',
    ]);
});

route()->get('/test', function (ServerRequestInterface $request, ResponseInterface $response) {
    $a = session()->get('a');
    session()->set('a', $a + 1);
    return json($response, [session_id(), $_SESSION]);
});

require_once __DIR__ . '/../route_auth.php';

/** @var ResponseInterface $response */
$response = $dispatcher->dispatch(env()->get('http.request'));
env()->get('http.response_sender')->send($response);
