<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/7
 * Time: 下午4:03
 */

namespace TCG\Auth\Session;


class FlashSegment extends Segment
{
    /**
     * @param mixed $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $data = parent::get($key, $default);
        $this->set($key, null);
        return $data;
    }
}