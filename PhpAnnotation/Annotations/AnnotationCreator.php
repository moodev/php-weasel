<?php
namespace PhpAnnotation\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on="method", max=1)
 */
class AnnotationCreator
{

    /**
     * @var array|null|\PhpAnnotation\Annotations\Parameter[]
     */
    protected $params;

    /**
     * @param \PhpAnnotation\Annotations\Parameter[]|null $params
     * @AnnotationCreator(@Parameter(name="params", type="\PhpAnnotation\Annotations\Parameter[]", required=false))
     */
    public function __construct(array $params) {
        $this->params = isset($params) ? $params : null;
    }

    /**
     * @return array|null|\PhpAnnotation\Annotations\Parameter[]
     */
    public function getParams()
    {
        return $this->params;
    }

}
