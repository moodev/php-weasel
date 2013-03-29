<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

class PropertyCreator extends Creator
{
    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\Param[]
     */
    public $params = array();

    public function __toString()
    {
        return "[PropertyCreator method={$this->method} params={" . implode(", ", $this->params) . "}]";
    }

}
