<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

use Weasel\Common\Cache\Cache;
use Weasel\Common\Cache\Exception;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Weasel\Common\Cache\CacheAwareInterface;

class AnnotationConfigurator implements AnnotationConfigProvider, LoggerAwareInterface, CacheAwareInterface
{

    /**
     * @var Config\AnnotationConfig
     */
    protected static $builtIns;

    protected $logger;

    /**
     * @var Cache
     */
    protected $cache;

    protected $ownedFactory = false;

    /**
     * @var AnnotationReaderFactory
     */
    protected $readerFactory = null;

    public function __construct(LoggerInterface $logger = null,
                                Cache $cache = null,
                                AnnotationReaderFactory $readerFactory = null)
    {
        if (isset($logger)) {
            $this->setLogger($logger);
        }
        if (isset($cache)) {
            $this->setCache($cache);
        }
        if (!isset(self::$builtIns)) {
            self::$builtIns = Config\BuiltInsProvider::getConfig();
        }
        if (isset($readerFactory)) {
            $this->setReaderFactory($readerFactory);
        } else {
            $readerFactory = new AnnotationReaderFactory($this);
            $this->ownedFactory = true;
            if (isset($logger)) {
                $readerFactory->setLogger($logger);
            }
            $this->setReaderFactory($readerFactory);
        }

    }

    public function get($name)
    {
        if (isset($this->cache)) {
            $found = false;
            $cached = $this->cache->get($name, "Annotation", $found);
            if ($found) {
                return $cached;
            }
        }

        if (self::$builtIns->getAnnotation($name)) {
            return self::$builtIns->getAnnotation($name);
        }

        $class = new \ReflectionClass($name);
        $reader = $this->readerFactory->getReaderForClass($class);

        /**
         * @var \Weasel\Annotation\Config\Annotations\Annotation $annotation
         */
        $annotation = $reader->getSingleClassAnnotation('\Weasel\Annotation\Config\Annotations\Annotation');
        if (!isset($annotation)) {
            throw new \RuntimeException("Did not find an @Annotation annotation on $name");
        }

        $metaConfig = new Config\Annotation($name, $annotation->getOn(), $annotation->getMax());

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            /**
             * @var \ReflectionMethod $method
             * @var \Weasel\Annotation\Config\Annotations\AnnotationCreator $creator
             */
            $creator = $reader->getSingleMethodAnnotation($method->getName(),
                '\Weasel\Annotation\Config\Annotations\AnnotationCreator'
            );
            if (isset($creator)) {
                if (!$method->isStatic() && !$method->isConstructor()) {
                    throw new \RuntimeException("Non-static methods cannot be configured as creators");
                }
                $metaConfig->setCreatorMethod($method->getName());
                $creatorArgs = $method->getParameters();
                if (count($creatorArgs) != count($creator->getParams())) {
                    throw new \RuntimeException("Creator args don't match with method args");
                }
                $i = 0;
                foreach ($creator->getParams() as $param) {
                    $paramName = $param->getName();
                    if (!isset($paramName)) {
                        $paramName = $creatorArgs[$i]->getName();
                    }
                    $creatorParam = new Config\Param($paramName, $param->getType(), $param->getRequired());
                    $i++;
                    $metaConfig->addCreatorParam($creatorParam);
                }

            }
        }

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            /**
             * @var \ReflectionProperty $property
             * @var \Weasel\Annotation\Config\Annotations\Property $annotProperty
             */
            $annotProperty = $reader->getSinglePropertyAnnotation($property->getName(),
                '\Weasel\Annotation\Config\Annotations\Property'
            );

            if (isset($annotProperty)) {
                $propertyConfig = new Config\Property($property->getName(), $annotProperty->getType());
                $metaConfig->addProperty($propertyConfig);
            }

            /**
             * @var \Weasel\Annotation\Config\Annotations\Enum $annotEnum
             */
            $annotEnum = $reader->getSinglePropertyAnnotation($property->getName(),
                '\Weasel\Annotation\Config\Annotations\Enum'
            );

            if (isset($annotEnum)) {
                if (!$property->isStatic()) {
                    throw new \RuntimeException("Enums must be static properties");
                }
                $name = $annotEnum->getName();
                if (!isset($name)) {
                    $name = $property->getName();
                }
                $value = $property->getValue(null);
                if (!is_array($value)) {
                    throw new \RuntimeException("Enum must be an array");
                }
                $metaConfig->addEnum(new Config\Enum($name, $value));
            }

        }

        if (isset($this->cache)) {
            $this->cache->set($name, $metaConfig, "Annotation");
        }

        return $metaConfig;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \Weasel\Annotation\AnnotationReaderFactory $readerFactory
     * @return AnnotationConfigurator
     */
    public function setReaderFactory($readerFactory)
    {
        $this->readerFactory = $readerFactory;
        return $this;
    }

    /**
     * @param \Weasel\Common\Cache\Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        if ($this->ownedFactory) {
            $this->readerFactory->setLogger($logger);
        }
    }
}
