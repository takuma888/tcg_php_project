<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/6
 * Time: 上午11:27
 */

namespace TCG\Auth\Session;


class Segment implements SegmentInterface
{
    /**
     *
     * The name of the $_SESSION segment, typically a class name.
     *
     * @var string
     *
     */
    protected $name;
    /**
     *
     * Constructor.
     *
     * @param string $name The name of the $_SESSION segment.
     *
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
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
    public function get($key, $default = null)
    {
        if (isset($_SESSION[$this->name][$key])) {
            return $_SESSION[$this->name][$key];
        }
        return $default;
    }
    /**
     *
     * Sets a value in the segment.
     *
     * @param mixed $key The key in the segment.
     *
     * @param mixed $val The value to set.
     *
     */
    public function set($key, $val)
    {
        if (!isset($_SESSION)) {
            return;
        }
        if ($val === null && isset($_SESSION[$this->name][$key])) {
            unset($_SESSION[$this->name][$key]);
        } else {
            $_SESSION[$this->name][$key] = $val;
        }
    }
}