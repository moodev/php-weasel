<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\IAnnotations;

interface IXmlElementWrapper
{

    public function getName();

    public function getNamespace();

    public function getRequired();

    public function getNillable();

}

