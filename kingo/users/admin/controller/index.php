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
    $username = trim($username);
    $password = $requestBody['password'];
    $password = trim($password);

    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    $userInfo = $userService->getUserInfoByUsername($username, true);
    if (!$userInfo['data']) {
        throw new \Exception("用户名不存在");
    }
    if ($userInfo['data']['password'] != md5($password)) {
        throw new \Exception("密码错误");
    }

    // 记录登录时间
    $userService->updateUser($userInfo['data']['id'], [
        'login_at' => date('Y-m-d H:i:s'),
    ]);
    session()->set('uid', $userInfo['data']['id']);
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
        /** @var \Users\Service\UserService $userService */
        $userService = service(\Users\Service\UserService::class);
        $userInfo = $userService->getUserInfoById($uid, true);
        if (!$userInfo['data']) {
            throw new \Exception("用户名不存在");
        }
        session()->set('uid', $userInfo['data']['id']);
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