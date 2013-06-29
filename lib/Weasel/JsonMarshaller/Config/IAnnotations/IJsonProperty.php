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
     * @return string Name to use for the JSON field (optional.) For a property the default is lcfirst($propertyName)
     *                For a method any leading "get" or "set" will be stripped, and a leading "is" stripped if the
     *                type's a bool and the method is thought to be a getter (it has 1 param.)
     */
    public function getName();

    /**
     * @return string The Weasel type for the field.
     */
    public function getType();

    /**
     * @return bool Should strict type checking be used?
     */
    public function getStrict();

}

