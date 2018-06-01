<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 下午2:16
 */

namespace TCG\Http\Message;


interface HeadersInterface
{
    public function add($key, $value);

    public function normalizeKey($key);
}