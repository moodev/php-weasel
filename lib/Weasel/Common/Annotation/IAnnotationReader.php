<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Annotation;

/**
 * Something that reads annotation objects from a class.
 */
interface IAnnotationReader
{

    /**
     * @return array[]
     */
    public function getClassAnnotations();

    /**
     * @param string $annotation
     * @return null|object[]
     */
    public function getClassAnnotation($annotation);

    /**
     * @param string $annotation
     * @return object
     */
    public function getSingleClassAnnotation($annotation);

    /**
     * @param string $method
     * @return array[]
     */
    public function getMethodAnnotations($method);

    /**
     * @param string $method
     * @param string $annotation
     * @return null|object[]
     */
    public function getMethodAnnotation($method, $annotation);

    /**
     * @param string $method
     * @param string $annotation
     * @return null|object
     */
    public function getSingleMethodAnnotation($method, $annotation);

    /**
     * @param string $property
     * @return array[]
     */
    public function getPropertyAnnotations($property);

    /**
     * @param string $property
     * @param string $annotation
     * @return null|object[]
     */
    public function getPropertyAnnotation($property, $annotation);

    /**
     * @param string $property
     * @param string $annotation
     * @return null|object
     */
    public function getSinglePropertyAnnotation($property, $annotation);

}
