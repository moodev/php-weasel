<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;

/**
 * A subtype
 * @Annotation(on={"\Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes"})
 */
class Type
{

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @param string $value
     * @param string|null $name
     * @AnnotationCreator({@Parameter(name="value", type="string", required=true), @Parameter(name="name", type="string", required=false)})
     */
    public function __construct($value, $name)
    {
        $this->value = $value;
        $this->name = $name;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

}

