<?php
namespace Weasel\Annotation\Annotations;

use Weasel\Annotation\Annotations\Annotation;
use Weasel\Annotation\Annotations\AnnotationCreator;
use Weasel\Annotation\Annotations\Parameter;

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
