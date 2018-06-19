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
    $id = intval($id);

    $username = $gets['username'] ?? '';
    $username = trim($username);
    $email = $gets['email'] ?? '';
    $email = trim($email);
    $mobile = $gets['mobile'] ?? '';
    $mobile = trim($mobile);

    $createAt = $gets['create_at'] ?? [];
    $loginAt = $gets['login_at'] ?? [];

    $filterCondition = [];
    $filterParams = [];

    if ($id) {
        $filterCondition[] = '`id` = :id';
        $filterParams[':id'] = $id;
    }
    if ($username) {
        $filterCondition[] = '`username` = :username';
        $filterParams[':username'] = $username;
    }
    if ($email) {
        $filterCondition[] = '`email` = :email';
        $filterParams[':email'] = $email;
    }
    if ($mobile) {
        $filterCondition[] = '`mobile` = :mobile';
        $filterParams[':mobile'] = $mobile;
    }
    if (isset($createAt[0])) {
        $filterCondition[] = '(`create_at` >= :create_at_start OR `register_at` >= :create_at_start)';
        $filterParams[':create_at_start'] = $createAt[0];
    }
    if (isset($createAt[1])) {
        $filterCondition[] = '(`create_at` <= :create_at_end OR `register_at` <= :create_at_end)';
        $filterParams[':create_at_end'] = $createAt[0];
    }
    if (isset($loginAt[0])) {
        $filterCondition[] = '(`login_at` >= :login_at_start)';
        $filterParams[':login_at_start'] = $loginAt[0];
    }
    if (isset($loginAt[1])) {
        $filterCondition[] = '(`login_at` <= :login_at_end)';
        $filterParams[':login_at_end'] = $loginAt[1];
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
    try {
        $userService->deleteManyByIds($ids);
        flash()->success('删除成功');
    } catch (\Exception $e) {
        flash()->error($e->getMessage());
    }
    return json($response, []);
});