<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午2:12
 */

use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
/**
 * 注册容器到环境中
 */
$container = container(ENV_USERS);
if (!$container) {
    $container = new Container();
    env(ENV_USERS, $container);
}
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
    if (is_null($status) && $this->getStatusCode() === \TCG\Http\StatusCode::HTTP_OK) {
        $status = \TCG\Http\StatusCode::HTTP_FOUND;
    }
    if (!is_null($status)) {
        return $responseWithRedirect->withStatus($status);
    }
    return $responseWithRedirect;
}

/**
 * @param $tableBaseName
 * @return \TCG\MySQL\Table
 */
function table($tableBaseName)
{
    switch ($tableBaseName) {
        case 'user_auth':
            return env()->get('users.mysql.tables.user_auth');
            break;
        case 'session':
            return env()->get('users.mysql.tables.session');
            break;
    }
}


/**
 * @return \TCG\Auth\Session\Segment
 */
function session()
{
    return env()->get('session.main');
}

/**
 * @param $key
 * @param null $message
 * @return mixed
 */
function flash($key, $message = null)
{
    /** @var \TCG\Auth\Session\Segment $segment */
    $segment = env()->get('session.flash');
    if ($message) {
        $segment->set($key, $message);
    } else {
        $message = $segment->get($key);
        $segment->set($key, null);
        return $message;
    }
}

function dao($name)
{

}