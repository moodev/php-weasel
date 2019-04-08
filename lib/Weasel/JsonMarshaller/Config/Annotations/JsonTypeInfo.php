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
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonTypeInfo;

/**
 * @Annotation(on={"class", "method", "property"})
 */
class JsonTypeInfo extends NoUndeclaredProperties implements IJsonTypeInfo
{

    /**
     * @var int[string]
     * @Enum("Id")
     */
    public static $enumId = array(
        "CLASS" => self::ID_CLASS,
        "CUSTOM" => self::ID_CUSTOM,
        "MINIMAL_CLASS" => self::ID_MINIMAL_CLASS,
        "NAME" => self::ID_NAME,
        "NONE" => self::ID_NONE,
    );

    /**
     * @var int[string]
     * @Enum("As")
     */
    public static $enumAs = array(
        "PROPERTY" => self::AS_PROPERTY,
        "WRAPPER_ARRAY" => self::AS_WRAPPER_ARRAY,
        "WRAPPER_OBJECT" => self::AS_WRAPPER_OBJECT,
        "EXTERNAL_PROPERTY" => self::AS_EXTERNAL_PROPERTY,
    );

    /**
     * @var string
     */
    protected $use;

    /**
     * @var string
     */
    protected $include;

    /**
     * @var string
     */
    protected $property;

    /**
     * @var bool
     */
    protected $visible;

    /**
     * @var string
     */
    protected $defaultImpl;

    /**
     * @AnnotationCreator({@Parameter(name="use", type="integer", required=true), @Parameter(name="include", type="integer", required=false), @Parameter(name="property", type="string", required=false), @Parameter(name="visible", type="bool", required=false), @Parameter(name="defaultImpl", type="string", required=false)})
     * @param int $use
     * @param int $include
     * @param string $property
     * @param bool $visible
     * @param string $defaultImpl
     */
    public function __construct($use, $include = null, $property = null, $visible = false, $defaultImpl = null)
    {
        $this->use = $use;
        $this->include = isset($include) ? $include : self::$enumAs["PROPERTY"];
        $this->property = empty($property) ? null : $property;
        $this->visible = isset($visible) && $visible;
        $this->defaultImpl = $defaultImpl;
    }

    /**
     * @return string
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getUse()
    {
        return $this->use;
    }

    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @return string
     */
    public function getDefaultImpl()
    {
        return $this->defaultImpl;
    }
}
