<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

/**
 * Set what properties to include for a class when serializing to JSON.
 */
interface IJsonInclude
{

    const INCLUDE_ALWAYS = 1; // Always include every property
    const INCLUDE_NON_DEFAULT = 2; // Include any property not set to it's default value.
    const INCLUDE_NON_EMPTY = 3; // Include any property that isn't empty()
    const INCLUDE_NON_NULL = 4; // Include any property that isn't === null

    /**
     * @return int One of the INCLUDE_ consts.
     */
    public function getValue();

}
