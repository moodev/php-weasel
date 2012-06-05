<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Annotations as Annotations;
use Weasel\Annotation\AnnotationReader;

/**
 * A config provider that uses Annotations
 */
class ArrayCachingAnnotationDriver extends AnnotationDriver
{
    protected $config = array();


    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    public function getConfig($class)
    {
        if (!isset($this->config[$class])) {
            $this->config[$class] = parent::getConfig($class);
        }

        return $this->config[$class];

    }

}
