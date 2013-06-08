<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Serialization\ClassSerialization;

class JsonBootstrapConfigProvider extends PropertyConfigProvider implements JsonConfigProvider
{

    private static $_bootstrapConfig = array();

    private function _buildBootstrapConfig()
    {
        return unserialize(file_get_contents(__DIR__ . '/json_marshaller.cnf'));
    }

    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    public function getConfig($class)
    {
        if (!isset(self::$_bootstrapConfig)) {
            $this->_buildBootstrapConfig();
        }
        if (!isset($this->config)) {
            $this->config = self::$_bootstrapConfig;
        }

        return parent::getConfig($class);
    }
}