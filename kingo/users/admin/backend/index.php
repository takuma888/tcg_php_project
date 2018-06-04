<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/4
 * Time: 下午2:23
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

route()->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    return redirect('http://admin.users.kingo.com', 301);
});