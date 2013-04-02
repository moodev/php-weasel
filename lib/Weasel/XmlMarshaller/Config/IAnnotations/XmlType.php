<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\IAnnotations;

interface IXmlType
{

    /**
     * @return string
     */
    public function getFactoryClass();

    /**
     * @return string
     */
    public function getFactoryMethod();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getNamespace();

    public function getPropOrder();

}

