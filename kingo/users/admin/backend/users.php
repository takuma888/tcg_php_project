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

});


/**
 * post /users/delete
 * 批量删除用户
 */
route()->post('/users/delete', function (ServerRequestInterface $request, ResponseInterface $response) {

});