<?php
namespace PhpJsonMarshaller\Config;

use PhpJsonMarshaller\Config\Annotations as Annotations;
use PhpAnnotation\AnnotationReader;

class AnnotationDriver implements ConfigProvider
{

    protected $classPaths = array();
    protected $configurator;

    public function __construct($logger = null) {
        $this->configurator = new \PhpAnnotation\ArrayCachingAnnotationConfigurator($logger);
    }

    /**
     * @param string $class
     * @return \JsonMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class) {
        $rClass = new \ReflectionClass($class);

        $classDriver = new ClassAnnotationDriver($rClass, $this->configurator);

        return $classDriver->getConfig();

    }

}
