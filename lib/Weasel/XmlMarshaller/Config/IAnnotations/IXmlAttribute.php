<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\IAnnotations;

interface IXmlAttribute
{
    public function getName();

    public function getType();

    public function getNamespace();

    public function getRequired();
}

