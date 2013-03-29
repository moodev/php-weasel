<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Weasel\Common\Utils\NoUndeclaredProperties;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 */
class JsonTypeInfo extends NoUndeclaredProperties
{

    const ID_CLASS = 1;
    const ID_CUSTOM = 2;
    const ID_MINIMAL_CLASS = 3;
    const ID_NAME = 4;
    const NONE = 5;

    const AS_PROPERTY = 1;
    const AS_WRAPPER_ARRAY = 2;
    const AS_WRAPPER_OBJECT = 3;
    const AS_EXTERNAL_PROPERTY = 4;

    /**
     * @var int
     */
    public $use = self::ID_CLASS;

    /**
     * @var int
     */
    public $include = self::AS_PROPERTY;

    /**
     * @var string
     */
    public $property = '@type';

    /**
     * @var bool
     */
    public $visible = false;

    /**
     * @var string
     */
    public $defaultImpl;

    /**
     * @return string
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getUse()
    {
        return $this->use;
    }

    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @return string
     */
    public function getDefaultImpl()
    {
        return $this->defaultImpl;
    }
}
