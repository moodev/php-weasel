<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
