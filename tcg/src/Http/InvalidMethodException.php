<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 下午2:37
 */

namespace TCG\Http;


use Psr\Http\Message\ServerRequestInterface;


class InvalidMethodException extends \InvalidArgumentException
{
    protected $request;

    public function __construct(ServerRequestInterface $request, $method)
    {
        $this->request = $request;
        parent::__construct(sprintf('Unsupported HTTP method "%s" provided', $method));
    }

    public function getRequest()
    {
        return $this->request;
    }
}