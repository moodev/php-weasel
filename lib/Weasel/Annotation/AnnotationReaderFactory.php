<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
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
