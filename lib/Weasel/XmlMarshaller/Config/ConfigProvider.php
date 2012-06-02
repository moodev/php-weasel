<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \Weasel\XmlMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
