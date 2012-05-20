<?php
namespace PhpMarshaller\Config;

use PhpMarshaller\Config\Annotations as Annotations;
use PhpAnnotation\AnnotationReader;

class AnnotationDriver
{

    protected $classPaths = array();
    protected $configurator;

    protected function _includeFiles() {
        foreach ($this->classPaths as $path) {
            $itt = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach(new \RegexIterator($itt, '/.php$/', \RecursiveRegexIterator::GET_MATCH) as $file) {
                @include($file);
            }
        }
    }

    public function __construct() {
        $this->configurator = new \PhpAnnotation\ArrayCachingAnnotationConfigurator();
    }

    /**
     * @param string $class
     * @return \PhpMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class) {
        $rClass = new \ReflectionClass($class);

        $classDriver = new ClassAnnotationDriver($rClass, $this->configurator);

        return $classDriver->getConfig();

    }

}
