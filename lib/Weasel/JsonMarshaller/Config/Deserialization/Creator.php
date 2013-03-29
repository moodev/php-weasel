<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

abstract class Creator
{

    /**
     * @var string
     */
    public $method;

    public function __toString()
    {
        return '[Creator method=' . $this->method . ']';
    }

}
