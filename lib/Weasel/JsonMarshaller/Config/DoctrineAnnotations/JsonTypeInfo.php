<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Weasel\Common\Utils\NoUndeclaredProperties;
use Doctrine\Common\Annotations\Annotation;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonTypeInfo;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 */
class JsonTypeInfo extends NoUndeclaredProperties implements IJsonTypeInfo
{

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
