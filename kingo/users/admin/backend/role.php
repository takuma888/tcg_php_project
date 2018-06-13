<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/9
 * Time: 下午5:51
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /role
 * 获取角色数据
 */
route()->get('/role/{id:\s+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {

});


/**
 * post /role/add
 * 添加角色
 */
route()->post('/role/add', function (ServerRequestInterface $request, ResponseInterface $response) {

});

/**
 * post /role/edit
 * 修改角色数据
 */
route()->post('/role/edit/{id:\s+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {

});

/**
 * post /role/delete
 * 删除角色数据
 */
route()->post('/role/delete/{id:\s+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {

});


