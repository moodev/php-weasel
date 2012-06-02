<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Annotation;

class AnnotationReaderFactory
{

    public function getReaderForClass(\ReflectionClass $class, AnnotationConfigProvider $configProvider)
    {
        return new AnnotationReader($class, $configProvider);
    }

}
