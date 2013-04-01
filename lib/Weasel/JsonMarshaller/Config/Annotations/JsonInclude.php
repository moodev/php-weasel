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
use Weasel\Common\Utils\NoUndeclaredProperties;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonInclude;

/**
 * @Annotation(on={"class", "method", "property"}, max=1)
 */
class JsonInclude extends NoUndeclaredProperties implements IJsonInclude
{

    /**
     * @var int[string]
     * @Enum("Include")
     */
    public static $enumInclude = array(
        "ALWAYS" => self::INCLUDE_ALWAYS,
        "NON_DEFAULT" => self::INCLUDE_NON_DEFAULT,
        "NON_EMPTY" => self::INCLUDE_NON_EMPTY,
        "NON_NULL" => self::INCLUDE_NON_NULL
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
