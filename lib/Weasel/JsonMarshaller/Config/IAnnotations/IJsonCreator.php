<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

/**
 * Mark a static factory method or constructor, and define the params to call it with.
 */
interface IJsonCreator
{

    /**
     * @return IJsonProperty[] An array of IJsonProperty defining the params to call the method with.
     */
    public function getParams();

}
