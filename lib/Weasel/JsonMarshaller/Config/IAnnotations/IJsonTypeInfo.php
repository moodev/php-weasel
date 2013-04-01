<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

interface IJsonTypeInfo
{

    const ID_CLASS = 1;
    const ID_CUSTOM = 2;
    const ID_MINIMAL_CLASS = 3;
    const ID_NAME = 4;
    const ID_NONE = 5;

    const AS_PROPERTY = 1;
    const AS_WRAPPER_ARRAY = 2;
    const AS_WRAPPER_OBJECT = 3;
    const AS_EXTERNAL_PROPERTY = 4;

    /**
     * @return int
     */
    public function getInclude();

    /**
     * @return string
     */
    public function getProperty();

    /**
     * @return int
     */
    public function getUse();

    /**
     * @return bool
     */
    public function getVisible();

    /**
     * @return string
     */
    public function getDefaultImpl();

}
