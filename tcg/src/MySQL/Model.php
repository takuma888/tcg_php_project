<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/5
 * Time: 下午12:33
 */

namespace TCG\MySQL;

use ArrayAccess;
use ReflectionClass;

abstract class Model implements ArrayAccess
{

    /**
     * @var array
     */
    private $_modified = [];

    /**
     * @param $key
     * @return string
     */
    public function key2Property($key)
    {
        $property = lcfirst($this->camelcase($key));
        return $property;
    }
    /**
     * @param $property
     * @return string
     */
    public function property2Key($property)
    {
        $field = $this->underscore($property);
        return $field;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function toArray()
    {
        $return = [];
        $rc = new ReflectionClass($this);
        foreach ($rc->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
            $property = $property->getName();
            $field = $this->property2Key($property);
            $return[$field] = $this->{$property};
        }
        return $return;
    }

    /**
     * @param $field
     * @return bool|string
     */
    public function hasProperty($field)
    {
        $property = $this->key2Property($field);
        if (property_exists($this, $property)) {
            return $property;
        }
        return false;
    }

    /**
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function set($key, $value)
    {
        $old_value = $this->get($key);
        $setter = 'set' . $this->camelcase($key);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else {
            if (($property = $this->hasProperty($key)) != false) {
                $this->{$property} = $value;
            }
        }
        // 检查数据变化情况
        if ($old_value != $value) {
            $this->_modified[$key] = $value;
        }
    }
    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function get($key)
    {
        $getter = 'get' . $this->camelcase($key);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (($property = $this->hasProperty($key)) != false) {
            return $this->{$property};
        } else {
            throw new \Exception("field {$key} not exists in model field list: [" . implode(', ', $this->getFields()) . "]");
        }
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        try {
            return $this->get($offset) != null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $offset
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function __get($key)
    {
        $key = $this->underscore($key);
        return $this->get($key);
    }

    /**
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function __set($key, $value)
    {
        $key = $this->underscore($key);
        $this->set($key, $value);
    }


    /**
     * 小写下划线模式
     * @param $string
     * @return string
     */
    public static function underscore($string)
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), strtr($string, '_', '.')));
    }
    /**
     * 大小写驼峰模式，首字母大写
     * @param $string
     * @return string
     */
    public static function camelcase($string)
    {
        return strtr(ucwords(strtr($string, array('_' => ' ', '.' => '_ ', '\\' => '_ '))), array(' ' => ''));
    }
}