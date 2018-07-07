<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/1
 * Time: 下午2:06
 */

namespace TCG\Http;


interface CollectionInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    public function set($key, $value);

    public function get($key, $default = null);

    public function replace(array $items);

    public function all();

    public function has($key);

    public function remove($key);

    public function clear();
}