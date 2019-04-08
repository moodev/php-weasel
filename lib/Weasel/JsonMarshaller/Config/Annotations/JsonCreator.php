<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;
use Weasel\Common\Utils\NoUndeclaredProperties;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonCreator;

/**
 * @Annotation(on="method", max=1)
 */
class JsonCreator extends NoUndeclaredProperties implements IJsonCreator
{

    /**
     * @var \Weasel\JsonMarshaller\Config\Annotations\JsonProperty[]
     */
    protected $params = array();

    /**
     * @param \Weasel\JsonMarshaller\Config\Annotations\JsonProperty[] params
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
