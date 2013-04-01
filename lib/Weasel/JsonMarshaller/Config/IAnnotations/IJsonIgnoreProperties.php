<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

/**
 * Configure the ignoring of unknown JSON elements for a class.
 */
interface IJsonIgnoreProperties
{

    /**
     * @return bool True if we should silently ignore all unknowns, false if not.
     */
    public function getIgnoreUnknown();

    /**
     * @return string[] List of JSON properties we should silently ignore.
     */
    public function getNames();

}
