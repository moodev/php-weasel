<?php
namespace PhpMarshaller\Config;

use PhpMarshaller\Config\Annotations as Annotations;
use PhpAnnotation\AnnotationReader;

class AnnotationDriver
{
    const _ANS = '\PhpMarshaller\Config\Annotations\\';

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
     */
    public function getConfig($class) {
        $rClass = new \ReflectionClass($class);

        $classDriver = new ClassAnnotationDriver($class, $this->configurator);

        return $classDriver->getConfig();

    }

}
