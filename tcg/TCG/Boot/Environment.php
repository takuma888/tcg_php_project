<?php

namespace TCG\Boot;

use Acclimate\Container\CompositeContainer;
use Acclimate\Container\ContainerAcclimator;
use Psr\Container\ContainerInterface;

class Environment
{

    /**
     * @var object
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
        self::$containers[$namespace] = $container;
    }


    /**
     * @param $namespaceBitExpr
     * @return CompositeContainer
     */
    public static function getContainer($namespaceBitExpr)
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
                    $container = self::$containers[$namespace];
                    $interface = self::getAcclimator()->acclimate($container);
                    self::$interfaces[$namespace] = $interface;
                }
                $interfaces[] = self::$interfaces[$namespace];
            }
            $composite = new CompositeContainer($interfaces);
            self::$composites[$bitString] = $composite;
        }
        return self::$composites[$bitString];
    }


    /**
     * @return ContainerAcclimator
     */
    protected static function getAcclimator()
    {
        if (!isset(self::$acclimator)) {
            self::$acclimator = new ContainerAcclimator();
        }
        return self::$acclimator;
    }
}