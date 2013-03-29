<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Weasel\Common\Utils\NoUndeclaredProperties;
use Doctrine\Common\Annotations\Annotation;

/**
 * Provides a list of properties not to consider when serializing/deserializing.
 * If ignoreUnknown is true then errors will not be thrown when we encounter properties to deserialize that we do not have a property for,
 * they will just be discarded.
 *
 * @Annotation
 * @Target("CLASS")
 */
class JsonIgnoreProperties extends NoUndeclaredProperties
{

    /**
     * @return bool
     */
    public function getIgnoreUnknown()
    {
        return $this->ignoreUnknown;
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @var string[]
     */
    public $names = array();

    /**
     * @var bool
     */
    public $ignoreUnknown = false;

    /**
     * @param array $values
     */
    public function __construct($values)
    {
        if (isset($values["names"])) {
            $this->names = $values["names"];
        } elseif (isset($values["value"])) {
            if (!is_array($values["value"])) {
                $this->names = array($values["value"]);
            } else {
                $this->names = $values["value"];
            }
        }
        if (isset($values["ignoreUnknown"])) {
            $this->ignoreUnknown = $values["ignoreUnknown"];
        }
    }
}
