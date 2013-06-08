<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Serialization\ClassSerialization;

class SerializedConfigProvider implements JsonConfigProvider
{

    private static $_bootstrapConfig = null;

    private $configFile = null;

    public function __construct($filename)
    {
        $this->configFile = $filename;
    }

    private function _buildBootstrapConfig()
    {
        $file = $this->configFile;
        self::$_bootstrapConfig[$file] = unserialize(file_get_contents($file));
    }

    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    public function getConfig($class)
    {
        $class = ltrim($class, '\\');
        $file = $this->configFile;
        if (!isset(self::$_bootstrapConfig[$file])) {
            $this->_buildBootstrapConfig();
        }
        return isset(self::$_bootstrapConfig[$file][$class]) ? self::$_bootstrapConfig[$file][$class] : null;
    }

}