<?php
namespace Weasel\Annotation\Annotations;

use Weasel\Annotation\Annotations\Annotation;
use Weasel\Annotation\Annotations\Parameter;

/**
 * @Annotation(on="method", max=1)
 */
class AnnotationCreator
{

    /**
     * @var array|null|\Weasel\Annotation\Annotations\Parameter[]
     */
    protected $params;

    /**
     * @param \Weasel\Annotation\Annotations\Parameter[]|null $params
     * @AnnotationCreator(@Parameter(name="params", type="\Weasel\Annotation\Annotations\Parameter[]", required=false))
     */
    public function __construct(array $params) {
        $this->params = isset($params) ? $params : null;
    }

    /**
     * @return array|null|\Weasel\Annotation\Annotations\Parameter[]
     */
    public function getParams()
    {
        return $this->params;
    }

}
