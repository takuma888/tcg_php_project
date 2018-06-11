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

    // 进行查询

});


/**
 * post /users/delete
 * 批量删除用户
 */
route()->post('/users/delete', function (ServerRequestInterface $request, ResponseInterface $response) {

});