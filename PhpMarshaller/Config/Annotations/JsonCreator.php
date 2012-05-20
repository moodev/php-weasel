<?php
namespace PhpMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on="method", max=1)
 */
class JsonCreator
{

    /**
     * @var array|null|\PhpMarshaller\Config\Annotations\JsonProperty[]
     */
    protected $params;

    /**
     * @param array|null|\PhpMarshaller\Config\Annotations\JsonProperty[] params
     * @AnnotationCreator(@Parameter(name="params", type="\PhpMarshaller\Config\Annotations\JsonProperty", required=false))
     */
    public function __construct(array $params) {
        $this->params = isset($params) ? $params : null;
    }


}
