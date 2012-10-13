<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Utils;

class ReflectionUtils
{

    /**
     * Call a static method with some args.
     * We avoid reflection for up to 4 args.
     * @param string $class
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function invokeStaticMethod($class, $method, $args)
    {
        switch (count($args)) {
            case 0:
                return $class::$method();
            case 1:
                return $class::$method($args[0]);
            case 2:
                return $class::$method($args[0], $args[1]);
            case 3:
                return $class::$method($args[0], $args[1], $args[2]);
            case 4:
                return $class::$method($args[0], $args[1], $args[2], $args[3]);
            default:
                $rMethod = new \ReflectionMethod($class, $method);
                return $rMethod->invokeArgs(null, $args);
        }
    }

    /**
     * Instantiate a class with some constructor args.
     * We avoid reflection for up to 4 args.
     * @param string $class
     * @param array $args
     * @return mixed
     */
    public static function instantiateClassByConstructor($class, $args)
    {
        switch (count($args)) {
            case 0:
                return new $class();
            case 1:
                return new $class($args[0]);
            case 2:
                return new $class($args[0], $args[1]);
            case 3:
                return new $class($args[0], $args[1], $args[2]);
            case 4:
                return new $class($args[0], $args[1], $args[2], $args[3]);
            default:
                $rClass = new \ReflectionClass($class);
                return $rClass->newInstanceArgs($args);
        }
    }

}
