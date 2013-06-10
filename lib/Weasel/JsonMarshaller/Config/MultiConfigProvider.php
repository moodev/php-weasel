<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace Weasel\JsonMarshaller\Config;

/**
 * Allow you to have multiple config sources, all working together to provide one config.
 * This would be useful if you've broken your JSON config up into multiple different files for some reason.
 * @package Weasel\JsonMarshaller\Config
 */
class MultiConfigProvider implements JsonConfigProvider
{

    /**
     * @var JsonConfigProvider[]
     */
    private $providers = array();

    /**
     * @param JsonConfigProvider[] $providers Array of providers
     */
    public function __construct(array $providers = array())
    {
        $this->providers = $providers;
    }

    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    public function getConfig($class)
    {
        foreach ($this->providers as $provider) {
            $config = $provider->getConfig($class);
            if ($config) {
                return $config;
            }
        }
        return null;
    }
}