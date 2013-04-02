<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations\JsonSubTypes;

/**
 * Type values for use in IJsonSubTypes;
 */
interface IType
{

    /**
     * @return string The fully qualified class which is our subtype (required.)
     */
    public function getValue();

    /**
     * @return string The name to use for this subtype (optional.)
     */
    public function getName();

}

