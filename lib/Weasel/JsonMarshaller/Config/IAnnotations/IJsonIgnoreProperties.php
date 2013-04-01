<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

interface IJsonIgnoreProperties
{

    /**
     * @return bool
     */
    public function getIgnoreUnknown();

    /**
     * @return string[]
     */
    public function getNames();

}
