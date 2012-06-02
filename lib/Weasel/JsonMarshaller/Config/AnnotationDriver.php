<?php
namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Annotations as Annotations;
use Weasel\Annotation\AnnotationReader;

class AnnotationDriver implements ConfigProvider
{

    protected $classPaths = array();
    protected $configurator;

    public function __construct($logger = null) {
        $this->configurator = new \Weasel\Annotation\ArrayCachingAnnotationConfigurator($logger);
    }

    /**
     * @param string $class
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class) {
        $rClass = new \ReflectionClass($class);

        $classDriver = new ClassAnnotationDriver($rClass, $this->configurator);

        return $classDriver->getConfig();

    }

}
