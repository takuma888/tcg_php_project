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

    /**
     * @param mixed $key
     * @param mixed $val
     */
    public function set($key, $val)
    {
        if (!isset($_SESSION)) {
            return;
        }
        if ($val === null && isset($_SESSION[$this->name][$key])) {
            unset($_SESSION[$this->name][$key]);
        } else {
            if (!isset($_SESSION[$this->name][$key])) {
                $_SESSION[$this->name][$key] = [];
            }
            $_SESSION[$this->name][$key][] = $val;
        }
    }


    public function error($val)
    {
        $this->set('error', $val);
    }


    public function warning($val)
    {
        $this->set('warning', $val);
    }


    public function info($val)
    {
        $this->set('info', $val);
    }

    public function success($val)
    {
        $this->set('success', $val);
    }
}