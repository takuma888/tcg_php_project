<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 上午10:06
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * post /login
 * 登录
 */
route()->post('/login', function (ServerRequestInterface $request, ResponseInterface $response) {
    $requestBody = $request->getParsedBody();
    $username = $requestBody['username'];
    $password = $requestBody['password'];

    // 使用dao获取数据

    return json($response, [
        'user' => [
            'uid' => 1,
            'username' => 'test_user',
            'avatar' => '',
        ],
    ]);
});


/**
 * get /logout
 * 退出
 */
route()->get('/logout', function (ServerRequestInterface $request, ResponseInterface $response) {
    session()->set('uid', null);
    return json($response, []);
});


/**
 * get /session
 * 获取session
 */
route()->get('/session', function (ServerRequestInterface $request, ResponseInterface $response) {
    $uid = session()->get('uid', null);
    if ($uid) {
        return json($response, [
            'user' => [
                'uid' => 1,
                'username' => 'test_user',
                'avatar' => '',
            ],
        ]);
    }
    return json($response, []);
});