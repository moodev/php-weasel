<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\Annotation;
use Weasel\Common\Utils\NoUndeclaredProperties;

/**
 * @Annotation
 * @Target("METHOD")
 */

class JsonCreator extends NoUndeclaredProperties
{

    /**
     * @var array
     */
    public $params = array();

    /**
     * @return JsonProperty[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $values
     */
    public function __construct($values)
    {
        if (isset($values["params"])) {
            $this->params = $values["params"];
        } elseif (isset($values["value"])) {
            if (!is_array($values["value"])) {
                $this->params = array($values["value"]);
            } else {
                $this->params = $values["value"];
            }
        }
    }


}
