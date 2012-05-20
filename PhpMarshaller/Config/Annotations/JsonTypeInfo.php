<?php
namespace PhpMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;
use PhpAnnotation\Annotations\Enum;

/**
 * @Annotation(on={"class"})
 */
class JsonTypeInfo
{

    /**
     * @var int[string]
     * @Enum("Id")
     */
    public static $enumId = array(
            "CLASS" => 1,
            "CUSTOM" => 2,
            "MINIMAL_CLASS" => 3,
            "NAME" => 4,
            "NONE" => 5
        );

    /**
     * @var int[string]
     * @Enum("As")
     */
    public static $enumAs = array(
        "PROPERTY" => 1,
        "WRAPPER_ARRAY" => 2,
        "WRAPPER_OBJECT" => 3
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
     * @AnnotationCreator({@Parameter(name="use", type="integer", required=true), @Parameter(name="include", type="integer", required=false), @Parameter(name="property", type="string", required=false)})
     * @param $use
     * @param $include
     * @param $property
     */
    public function __construct($use, $include, $property)
    {
        $this->use = $use;
        $this->include = $include;
        $this->property = $property;
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
}
