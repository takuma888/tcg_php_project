<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 下午2:11
 */

namespace TCG\Http\Message;


interface EnvironmentInterface
{
    public static function mock(array $settings = []);
}