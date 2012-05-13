<?php
namespace PhpAnnotation\Annotations;

/**
 * @Annotation(on="method", max=1)
 */
class AnnotationCreator
{

    protected $params = array();

    /**
     * @param \PhpAnnotation\Annotations\Parameter[]|null $params
     * @AnnotationCreator(@Parameter(name="params", type="\PhpAnnotation\Annotations\Parameter[]", required=false))
     */
    public function __construct(array $params = array()) {
        $this->params = $params;
    }

}
