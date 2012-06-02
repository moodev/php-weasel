<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;

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
    public function __construct(array $params)
    {
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
