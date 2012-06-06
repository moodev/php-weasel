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
use Weasel\Annotation\Config\Annotations\Enum;

/**
 * @Annotation(on={"class", "method", "property"}, max=1)
 */
class JsonInclude
{

    /**
     * @var int[string]
     * @Enum("Include")
     */
    public static $enumInclude = array(
        "ALWAYS" => 1,
        "NON_DEFAULT" => 2,
        "NON_EMPTY" => 3,
        "NON_NULL" => 4
    );

    /**
     * @var integer
     */
    protected $value;

    /**
     * @AnnotationCreator(@Parameter(name="value", type="integer", required=true))
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

}
