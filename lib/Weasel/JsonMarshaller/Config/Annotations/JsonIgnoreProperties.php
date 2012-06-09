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

/**
 * Provides a list of properties not to consider when serializing/deserializing.
 * If ignoreUnknown is true then errors will not be thrown when we encounter properties to deserialize that we do not have a property for,
 * they will just be discarded.
 *
 * @Annotation(on="class")
 */
class JsonIgnoreProperties
{

    /**
     * @param $names
     * @param $ignoreUnknown
     * @AnnotationCreator({
     * @Parameter(name="names", type="string[]", required=false),
     * @Parameter(name="ignoreUnknown", type="boolean", required=false)
     * })
     */
    public function __construct($names, $ignoreUnknown)
    {
        $this->names = isset($names) ? $names : array();
        $this->ignoreUnknown = isset($ignoreUnknown) ? $ignoreUnknown : false;
    }

    public function getIgnoreUnknown()
    {
        return $this->ignoreUnknown;
    }

    public function getNames()
    {
        return $this->names;
    }

    protected $names;

    protected $ignoreUnknown;

}
