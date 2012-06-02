<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Annotation\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;

/**
 * @Annotation(on="property")
 */
class Enum
{

    protected $name;

    /**
     * @param string $name
     * @AnnotationCreator(@Parameter(name="name", type="string", required=false))
     */
    public function __construct($name)
    {
        $this->name = isset($name) ? $name : null;
    }

    public function getName()
    {
        return $this->name;
    }

}
