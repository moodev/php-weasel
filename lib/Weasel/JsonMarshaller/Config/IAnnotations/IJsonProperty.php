<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

/**
 * Sets a property up to be serialized/deserialized explicitly.
 * The name sets the json field name to use for this property.
 * The type specifies the type to use.
 * Because PHP is not strongly typed we can only make best guesses about types if you do not provide type info!
 */
interface IJsonProperty
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

}

