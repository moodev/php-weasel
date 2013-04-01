<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Annotation;

interface IAnnotationReaderFactory
{

    /**
     * @param \ReflectionClass $class
     * @return \Weasel\Common\Annotation\IAnnotationReader
     */
    public function getReaderForClass(\ReflectionClass $class);

}
