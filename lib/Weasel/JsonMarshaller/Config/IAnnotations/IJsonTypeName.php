<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

/**
 * Allows you to specify an explicit name for this implementation, for use by JsonTypeInfo
 */
interface IJsonTypeName
{

    /**
     * @return string
     */
    public function getName();

}

