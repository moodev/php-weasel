<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;

/**
 * @Annotation(on="property", max=1)
 */
class Property
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $type
     * @AnnotationCreator
     * @CreatorParam(name="type", type="string", required=false)
     */
    public function __construct($type)
    {
        $this->type = isset($type) ? $type : "string";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
