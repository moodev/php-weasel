<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Cache\Exception;

use Weasel\Common\Cache\CacheException;

class NotFound extends CacheException
{
    public function __construct($key)
    {
        parent::__construct($key, "Key not found in store");
    }

}
