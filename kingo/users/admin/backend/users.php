<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午4:22
 */


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /users
 * 用户列表
 */
route()->get('/users', function (ServerRequestInterface $request, ResponseInterface $response) {
    $gets = $request->getQueryParams();
    // 分页
    $page = $gets['page'] ?? 1;
    $size = $gets['size'] ?? 20;
    // 其他查询条件
    $id = $gets['id'] ?? '';
    $username = $gets['username'] ?? '';
    $email = $gets['email'] ?? '';
    $mobile = $gets['mobile'] ?? '';

    $filterCondition = [];
    $filterParams = [];

    if ($id) {
        $filterCondition []= '`id` = :id';
        $filterParams[':id'] = $id;
    }
    if ($username) {
        $filterCondition []= '`username` = :username';
        $filterParams[':username'] = $username;
    }
    if ($email) {
        $filterCondition []= '`email` = :email';
        $filterParams[':email'] = $email;
    }
    if ($mobile) {
        $filterCondition []= '`mobile` = :mobile';
        $filterParams[':mobile'] = $mobile;
    }
    $limit = max(0, $size);
    $offset = (max(1, $page) - 1) * $limit;
    $filterCondition = implode(' AND ', $filterCondition);
    if (strlen($filterCondition)) {
        $filterCondition .= ' LIMIT ' . $offset . ', ' . $limit;
    } else {
        $filterCondition = 'WHERE 1 LIMIT ' . $offset . ', ' . $limit;
    }
    // 进行查询
    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    $result = $userService->authSelectMany($filterCondition, $filterParams);
    return json($response, $result);
});


/**
 * post /users/delete
 * 批量删除用户
 */
route()->post('/users/delete', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $ids = $posts['ids'] ?? [];

    /** @var \Users\Service\UserService $userService */
    $userService = service(\Users\Service\UserService::class);
    $userService->deleteManyByIds($ids);
    return json($response, []);
});