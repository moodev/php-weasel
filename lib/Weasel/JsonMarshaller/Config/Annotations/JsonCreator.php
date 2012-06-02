<?php
namespace Weasel\JsonMarshaller\Config\Annotations;

use Weasel\Annotation\Annotations\Annotation;
use Weasel\Annotation\Annotations\AnnotationCreator;
use Weasel\Annotation\Annotations\Parameter;

/**
 * @Annotation(on="method", max=1)
 */
class JsonCreator
{

    /**
     * @var \Weasel\JsonMarshaller\Config\Annotations\JsonProperty[]
     */
    protected $params = array();

    /**
     * @param null|\Weasel\JsonMarshaller\Config\Annotations\JsonProperty[] params
     * @AnnotationCreator(@Parameter(name="params", type="\Weasel\JsonMarshaller\Config\Annotations\JsonProperty[]", required=false))
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
