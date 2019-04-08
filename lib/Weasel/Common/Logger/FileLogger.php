<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * This exists as a lame shim so that anyone relying on it can move to a real logging framework.
 * @deprecated Use a real logger, like monolog!
 */
class FileLogger extends AbstractLogger implements Logger
{

    protected $_logLevel = 0;
    protected $_logFile;

    public function __construct($logFile = null)
    {
        if (!isset($logFile)) {
            $this->_logFile = 'php://stderr';
        } else {
            $this->_logFile = $logFile;
        }
    }

    public function setLogLevel($level)
    {
        $this->_logLevel = $level;
    }

    public function logDebug($entry)
    {
        $this->debug($entry);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->_logLevel >= self::LOG_LEVEL_DEBUG) {
            error_log($message . "\n", 3, $this->_logFile);
        }
    }
}
