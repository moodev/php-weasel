<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;
use Weasel\XmlMarshaller\Config\IAnnotations\IXmlDiscriminator;
use Weasel\Common\Utils\NoUndeclaredProperties;

/**
 * @Annotation(on={"class"})
 */
class XmlDiscriminator extends NoUndeclaredProperties implements IXmlDiscriminator
{

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value;
     * @AnnotationCreator({@Parameter(name="value", type="string", required=true)})
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


}

