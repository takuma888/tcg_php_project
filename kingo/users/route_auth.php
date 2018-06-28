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
    $username = $requestBody['username'] ?? '';
    $username = trim($username);
    $password = $requestBody['password'] ?? '';
    $password = trim($password);
    /** @var \Users\Service\AuthService $authService */
    $authService = service(\Users\Service\AuthService::class);
    $userInfo = $authService->loginByUsername($username, $password);
    flash()->success("欢迎回来~ {$userInfo['data']['username']}");
    return json($response, [
        'user' => [
            'uid' => $userInfo['data']['id'],
            'username' => $userInfo['data']['username'],
            'avatar' => '',
        ],
    ]);
});


/**
 * get /logout
 * 退出
 */
route()->get('/logout', function (ServerRequestInterface $request, ResponseInterface $response) {
    /** @var \Users\Service\AuthService $authService */
    $authService = service(\Users\Service\AuthService::class);
    $authService->logout();
    return json($response, []);
});


/**
 * get /session
 * 获取session
 */
route()->get('/session', function (ServerRequestInterface $request, ResponseInterface $response) {
    /** @var \Users\Service\AuthService $authService */
    $authService = service(\Users\Service\AuthService::class);
    $userInfo = $authService->session();
    if ($userInfo) {
        return json($response, [
            'user' => [
                'uid' => $userInfo['data']['id'],
                'username' => $userInfo['data']['username'],
                'avatar' => '',
            ],
        ]);
    } else {
        return json($response, []);
    }
});