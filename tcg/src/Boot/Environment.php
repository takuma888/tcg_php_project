<?php

namespace TCG\Boot;

use Acclimate\Container\CompositeContainer;
use Acclimate\Container\ContainerAcclimator;
use Composer\Autoload\ClassLoader;
use Psr\Container\ContainerInterface;

class Environment
{

    /**
     * @var object[]
     */
    protected static $containers = [];

    /**
     * @var CompositeContainer[]
     */
    protected static $composites = [];

    /**
     * @var ContainerInterface[]
     */
    protected static $interfaces = [];

    /**
     * @var ContainerAcclimator
     */
    protected static $acclimator;

    /**
     * @var string
     */
    protected static $namespace = '0';

    /**
     * @var ClassLoader
     */
    protected static $autoloader;

    /**
     * @param ClassLoader $loader
     */
    public static function setAutoloader(ClassLoader $loader)
    {
        self::$autoloader = $loader;
    }

    /**
     * @return ClassLoader
     */
    public static function getAutoloader()
    {
        return self::$autoloader;
    }


    /**
     * @param $namespace
     * @return string
     */
    public static function setNamespace($namespace)
    {
        $oldNamespace = self::$namespace;
        self::$namespace = $namespace;
        return $oldNamespace;
    }

    /**
     * @return string
     */
    public static function getNamespace()
    {
        return self::$namespace;
    }


    /**
     * @param $container
     * @param $namespace
     */
    public static function setContainer($container, $namespace = null)
    {
        if (!$namespace) {
            $namespace = self::getNamespace();
        }
        $namespace = decbin($namespace);
        self::$containers[$namespace] = $container;
    }


    /**
     * @param $namespace
     * @return object|null
     */
    public static function getContainer($namespace)
    {
        return isset(self::$containers[$namespace]) ? self::$containers[$namespace] : null;
    }

    /**
     * @return object[]
     */
    public static function getContainers()
    {
        return self::$containers;
    }


    /**
     * @param $namespaceBitExpr
     * @return CompositeContainer
     */
    public static function container($namespaceBitExpr)
    {
        $bitString = decbin($namespaceBitExpr);
        if (!isset(self::$composites[$bitString])) {
            $len = strlen($bitString);
            $bits = str_split($bitString);
            while ($bits) {
                $bit = array_shift($bits);
                $namespace = $bit . str_repeat('0', $len - 1);
                $len -= 1;
                if (isset(self::$containers[$namespace])) {
                    $namespaces[] = $namespace;
                }
            }
            $namespaces[] = '0';
            $namespaces = array_unique($namespaces);
            $interfaces = [];
            foreach ($namespaces as $namespace) {
                if (!isset(self::$interfaces[$namespace])) {
                    if (isset(self::$containers[$namespace])) {
                        $container = self::$containers[$namespace];
                        $interface = self::ContainerAcclimator()->acclimate($container);
                        self::$interfaces[$namespace] = $interface;
                        $interfaces[] = $interface;
                    }
                } else {
                    $interfaces[] = self::$interfaces[$namespace];
                }
            }
            if ($interfaces) {
                $composite = new CompositeContainer($interfaces);
                self::$composites[$bitString] = $composite;
            }

        }
        return isset(self::$composites[$bitString]) ? self::$composites[$bitString] : null;
    }


    /**
     * @return ContainerAcclimator
     */
    protected static function ContainerAcclimator()
    {
        if (!isset(self::$acclimator)) {
            self::$acclimator = new ContainerAcclimator();
        }
        return self::$acclimator;
    }
}