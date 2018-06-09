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

});

/**
 * post /roles/delete
 * 删除角色
 */
route()->post('/roles/delete' , function (ServerRequestInterface $request, ResponseInterface $response) {

});