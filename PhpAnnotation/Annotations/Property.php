<?php
namespace PhpAnnotation\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on="property", max=1)
 */
class Property
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $type
     * @AnnotationCreator
     * @CreatorParam(name="type", type="string", required=false)
     */
    public function __construct($type) {
        $this->type = isset($type) ? $type : "string";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
