<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

interface IAnnotationReaderFactory
{

    /**
     * @param \ReflectionClass $class
     * @return \Weasel\Annotation\IAnnotationReader
     */
    public function getReaderForClass(\ReflectionClass $class);

}
