<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * Class DelegateCreator
 * @package Weasel\JsonMarshaller\Config\Deserialization
 * @JsonTypeName("delegate")
 */
class DelegateCreator extends Creator
{

    public function __toString()
    {
        return '[DelegateCreator method=' . $this->method . ']';
    }

}
