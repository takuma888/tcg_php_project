<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 上午9:50
 */

use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

/**
 * 注册容器对象到环境中
 */
$container = new Container();
env(ENV_DEFAULT, $container);

// http
// tcg http environment
$container['tcg.http.environment'] = function () {
    return \TCG\Http\Environment::mock($_SERVER);
};
// tcg http request
$container['http.request'] = function (Container $c) {
    return \TCG\Http\Request::createFromEnvironment($c['tcg.http.environment']);
};

// tcg http response
$container['http.response'] = $container->factory(function () {
    return new \TCG\Http\Response();
});

$container['http.response_sender'] = function () {
    return new \TCG\Http\ResponseSender();
};

// 中间件
// dispatcher
$container['middleware.dispatcher'] = $container->factory(function () {
    return new \TCG\Middleware\Dispatcher();
});

// logger
$container['logger.file_root'] = __DIR__ . '/_log';
$container['logger'] = function (Container $c) {
    $logger = new Monolog\Logger('default');
    $logger->pushHandler(new Monolog\Handler\StreamHandler($c['logger.file_root'] . '/debug/' . date('Y-m-d') . '.log', \Monolog\Logger::DEBUG));
    return $logger;
};

/**
 * @return \Psr\Log\LoggerInterface
 */
function logger()
{
    return env()->get('logger');
}

// twig
$container['twig.engine.config.cache'] = false;
$container['twig.engine.config.debug'] = true;
$container['twig.loader.filesystem_loader'] = function () {
    return new \Twig_Loader_Filesystem();
};
$container['twig.extension.string_loader'] = function () {
    return new \Twig_Extension_StringLoader();
};
$container['twig'] = function (Container $c) {
    $engine = new \Twig_Environment($c['twig.loader.filesystem_loader'], [
        'cache' => $c['twig.engine.config.cache'],
        'debug' => $c['twig.engine.config.debug']
    ]);
    $engine->addExtension($c['twig.extension.string_loader']);
    return $engine;
};


// 定义一些简便方法

function json(ResponseInterface $response, array $data, $status = null, $encodingOptions = 0)
{
    $response = $response->withBody(new \TCG\Http\Body(fopen('php://temp', 'r+')));
    $response->getBody()->write($json = json_encode($data, $encodingOptions));
    // Ensure that the json encoding passed successfully
    if ($json === false) {
        throw new \RuntimeException(json_last_error_msg(), json_last_error());
    }
    $responseWithJson = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
    if (isset($status)) {
        return $responseWithJson->withStatus($status);
    }
    return $responseWithJson;
}

function redirect(ResponseInterface $response, $url, $status = null)
{
    $responseWithRedirect = $response->withHeader('Location', (string)$url);
    if (is_null($status) && $response->getStatusCode() === \TCG\Http\StatusCode::HTTP_OK) {
        $status = \TCG\Http\StatusCode::HTTP_FOUND;
    }
    if (!is_null($status)) {
        return $responseWithRedirect->withStatus($status);
    }
    return $responseWithRedirect;
}
