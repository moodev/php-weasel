<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;


/**
 * Mark a method as the "Any Setter"
 * This method will be called with a set of key => value pairs containing any data from the JSON that we didn't
 * recognise as belonging in another field.
 */
interface IJsonAnySetter
{

}
