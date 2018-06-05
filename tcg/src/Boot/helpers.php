<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/5/31
 * Time: 上午11:24
 */
use TCG\Boot\Environment;
use Composer\Autoload\ClassLoader;
/**
 * 辅助函数
 */

/**
 * 辅助的调试函数
 */
if (!function_exists('pre')) {
    /**
     * @param $var
     * @param bool|true $exit
     */
    function pre($var, $exit = true)
    {
        if (is_array($var) || is_object($var)) {
            $output = print_r($var, true);
        } else {
            $output = var_export($var, true);
        }
        if (PHP_SAPI == 'cli') {
            echo $output . "\n";
        } else {
            echo '<pre>', $output, '</pre>';
        }
        if ($exit) {
            exit();
        }
    }
}
if (!function_exists('tick')) {
    /**
     * @param $var
     * @throws Exception
     */
    function tick($var)
    {
        if (is_array($var) || is_object($var)) {
            $output = print_r($var, true);
        } else {
            $output = var_export($var, true);
        }
        echo $output, "\n";
    }
}



if (!function_exists('env')) {
    /**
     * @param null $namespaceBitExpr
     * @param null $container
     * @return \Acclimate\Container\CompositeContainer|array
     */
    function env($namespaceBitExpr = null, $container = null)
    {
        if ($namespaceBitExpr >= 0) {
            if ($container) {
                // 指定了容器对象则为设置容器对象的名称空间
                Environment::setContainer($container, $namespaceBitExpr);
            } else {
                // 获取容器
                return Environment::container($namespaceBitExpr | Environment::getNamespace());
            }
        }
    }
}


if (!function_exists('containers')) {
    /**
     * @return object[]
     */
    function containers()
    {
        return Environment::getContainers();
    }
}


if (!function_exists('container')) {
    /**
     * @param $namespace
     * @return null|object
     */
    function container($namespace)
    {
        return Environment::getContainer($namespace);
    }
}


if (!function_exists('app')) {
    /**
     * @param null $namespace
     * @return string
     */
    function app($namespace = null)
    {
        if ($namespace) {
            Environment::setNamespace($namespace);
        } else {
            return Environment::getNamespace();
        }
    }
}


if (!function_exists('loader')) {
    /**
     * @param ClassLoader|null $loader
     * @return ClassLoader
     */
    function loader(ClassLoader $loader = null)
    {
        if ($loader) {
            Environment::setAutoloader($loader);
        }
        return Environment::getAutoloader();
    }
}