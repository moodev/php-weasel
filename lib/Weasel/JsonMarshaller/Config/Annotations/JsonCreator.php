<?php
namespace PhpJsonMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on="method", max=1)
 */
class JsonCreator
{

    /**
     * @var \PhpJsonMarshaller\Config\Annotations\JsonProperty[]
     */
    protected $params = array();

    /**
     * @param null|\PhpJsonMarshaller\Config\Annotations\JsonProperty[] params
     * @AnnotationCreator(@Parameter(name="params", type="\JsonMarshaller\Config\Annotations\JsonProperty[]", required=false))
     */
    public function __construct(array $params) {
        $this->params = isset($params) ? $params : array();
    }

    /**
     * @return array|JsonProperty[]
     */
    public function getParams()
    {
        return $this->params;
    }


}
