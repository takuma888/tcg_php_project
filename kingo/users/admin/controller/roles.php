<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/9
 * Time: 下午5:34
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /roles
 * 获取角色列表
 */
route()->get('/roles', function (ServerRequestInterface $request, ResponseInterface $response) {
    $gets = $request->getQueryParams();
    // 分页
    $page = $gets['page'] ?? 1;
    $size = $gets['size'] ?? 20;
    // 其他查询条件
    $id = $gets['id'] ?? '';
    $name = $gets['name'] ?? '';

    $filterCondition = [];
    $filterParams = [];

    if ($id) {
        $filterCondition []= '`id` = :id';
        $filterParams[':id'] = $id;
    }
    if ($name) {
        $filterCondition []= '`name` LIKE %:name%';
        $filterParams[':name'] = $name;
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
    /** @var \Users\Service\RoleService $roleService */
    $roleService = service(\Users\Service\RoleService::class);
    $result = $roleService->selectMany($filterCondition, $filterParams);
    return json($response, $result);
});

/**
 * post /roles/delete
 * 删除角色
 */
route()->post('/roles/delete' , function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $ids = $posts['ids'] ?? [];
    /** @var \Users\Service\RoleService $roleService */
    $roleService = service(\Users\Service\RoleService::class);
    $roleService->deleteManyByIds($ids);
    return json($response, []);
});