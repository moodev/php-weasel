<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Logger;

interface Logger
{

    const LOG_LEVEL_OFF = 0;
    const LOG_LEVEL_DEBUG = 255;

    public function setLogLevel($level);

    public function logDebug($entry);

}
