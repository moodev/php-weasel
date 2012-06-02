<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Logger;

class FileLogger implements Logger
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
        if ($this->_logLevel >= self::LOG_LEVEL_DEBUG) {
            error_log($entry . "\n", 3, $this->_logFile);
        }
    }

}
