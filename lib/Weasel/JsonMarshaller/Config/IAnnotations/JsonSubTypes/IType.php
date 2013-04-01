<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations\JsonSubTypes;

interface IType
{

    public function getValue();

    /**
     * @return string
     */
    public function getName();

}

