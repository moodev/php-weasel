<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Annotation;

/**
 * A factory for annotation readers.
 */
interface IAnnotationReaderFactory
{

    /**
     * Obtain an annotation reader for the provided reflection class.
     * This is perfectly entitled to return the same instance of IAnnotationReader no matter what class you call it
     * with.
     * @param \ReflectionClass $class
     * @return \Weasel\Common\Annotation\IAnnotationReader
     */
    public function getReaderForClass(\ReflectionClass $class);

}
