<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午3:56
 */


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /user
 * 用户数据
 */
route()->get('/user[/{id:\d+}]', function (ServerRequestInterface $request, ResponseInterface $response, $id = null) {

});

/**
 * post /user/add
 * 添加用户
 */
route()->post('/user/add', function (ServerRequestInterface $request, ResponseInterface $response) {

});

/**
 * post /user/edit
 * 修改用户
 */
route()->post('/user/edit[/{id:\d+}]', function (ServerRequestInterface $request, ResponseInterface $response, $id = null) {

});

/**
 * post /user/delete
 * 删除用户
 */
route()->post('/user/delete[/{id:\d+}]', function (ServerRequestInterface $request, ResponseInterface $response, $id = null) {

});





