<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

/**
 * The list of subtypes of this base class.
 */
interface IJsonSubTypes
{

    /**
     * @return JsonSubTypes\IType[] The list of sub-types
     */
    public function getValue();

}

