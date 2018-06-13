<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午2:12
 */

use Psr\Http\Message\ResponseInterface;

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


/**
 * @return \TCG\Auth\Session\Segment
 */
function session()
{
    return env()->get('session.main');
}

/**
 * @return \TCG\Auth\Session\FlashSegment
 */
function flash()
{
    return env()->get('session.flash');
}