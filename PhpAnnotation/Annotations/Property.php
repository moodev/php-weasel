<?php
namespace PhpAnnotation\Annotations;

/**
 * @Annotation(on="property", max=1)
 */
class Property
{

    /**
     * @var string
     */
    protected $type = "string";

    /**
     * @param string $type
     * @AnnotationCreator
     * @CreatorParam(name="type", type="string", required=false)
     */
    public function __construct($type = "string") {
        $this->type = $type;
    }
}
