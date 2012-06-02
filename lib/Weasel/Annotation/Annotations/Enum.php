<?php
namespace Weasel\Annotation\Annotations;

use Weasel\Annotation\Annotations\Annotation;
use Weasel\Annotation\Annotations\AnnotationCreator;
use Weasel\Annotation\Annotations\Parameter;

/**
 * @Annotation(on="property")
 */
class Enum
{

    protected $name;

    /**
     * @param string $name
     * @AnnotationCreator(@Parameter(name="name", type="string", required=false))
     */
    public function __construct($name)
    {
        $this->name = isset($name) ? $name : null;
    }

    public function getName()
    {
        return $this->name;
    }

}

