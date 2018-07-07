<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/6
 * Time: 上午11:26
 */

namespace TCG\Auth\Session;


interface SegmentInterface
{
    /**
     *
     * Gets a value from the segment.
     *
     * @param mixed $key A key for the segment value.
     *
     * @param mixed $default Return this value if the segment key does not exist.
     *
     * @return mixed
     *
     */
    public function get($key, $default = null);
    /**
     *
     * Sets a value in the segment.
     *
     * @param mixed $key The key in the segment.
     *
     * @param mixed $val The value to set.
     *
     */
    public function set($key, $val);
}