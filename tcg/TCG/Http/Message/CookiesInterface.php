<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 下午2:13
 */

namespace TCG\Http\Message;


interface CookiesInterface
{
    public function get($name, $default = null);

    public function set($name, $value);

    public function toHeaders();

    public static function parseHeader($header);

}