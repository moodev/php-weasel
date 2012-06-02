<?php
namespace Weasel\Annotation;


class AnnotationConfigurator
{

    /**
     * @var Config\AnnotationConfig
     */
    protected static $builtIns;

    protected $logger;

    public function __construct(\Weasel\Logger\Logger $logger = null)
    {
        $this->logger = $logger;
        if (!isset(self::$builtIns)) {
            self::$builtIns = Config\BuiltInsProvider::getConfig();
        }
    }

    public function get($name)
    {
        if (self::$builtIns->getAnnotation($name)) {
            return self::$builtIns->getAnnotation($name);
        }

        $class = new \ReflectionClass($name);
        $reader = new AnnotationReader($class, $this);

        /**
         * @var \Weasel\Annotation\Config\Annotations\Annotation $annotation
         */
        $annotation = $reader->getSingleClassAnnotation('\Weasel\Annotation\Config\Annotations\Annotation');
        if (!isset($annotation)) {
            throw new \Exception("Did not find an @Annotation annotation on $name");
        }

        $metaConfig = new Config\Annotation($name, $annotation->getOn(), $annotation->getMax());

        foreach ($class->getMethods() as $method) {
            /**
             * @var \ReflectionMethod $method
             * @var \Weasel\Annotation\Config\Annotations\AnnotationCreator $creator
             */
            $creator = $reader->getSingleMethodAnnotation($method->getName(), '\Weasel\Annotation\Config\Annotations\AnnotationCreator');
            if (isset($creator)) {
                $metaConfig->setCreatorMethod($method->getName());
                $creatorArgs = $method->getParameters();
                if (count($creatorArgs) != count($creator->getParams())) {
                    throw new \Exception("Creator args don't match with method args");
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

        foreach ($class->getProperties() as $property) {
            /**
             * @var \ReflectionProperty $property
             * @var \Weasel\Annotation\Config\Annotations\Property $annotProperty
             */
            $annotProperty = $reader->getSinglePropertyAnnotation($property->getName(), '\Weasel\Annotation\Config\Annotations\Property');

            if (isset($annotProperty)) {
                $propertyConfig = new Config\Property($property->getName(), $annotProperty->getType);
                $metaConfig->addProperty($property->getName(), $propertyConfig);
            }

            /**
             * @var \Weasel\Annotation\Config\Annotations\Enum $annotEnum
             */
            $annotEnum = $reader->getSinglePropertyAnnotation($property->getName(), '\Weasel\Annotation\Config\Annotations\Enum');

            if (isset($annotEnum)) {
                if (!$property->isStatic()) {
                    throw new \Exception("Enums must be static properties");
                }
                $name = $annotEnum->getName();
                if (!isset($name)) {
                    $name = $property->getName();
                }
                $value = $property->getValue(null);
                if (!is_array($value)) {
                    throw new \Exception("Enum must be an array");
                }
                $metaConfig->addEnum($name, new Config\Enum($name, $value));
            }

        }

        return $metaConfig;
    }

    public function getLogger()
    {
        return $this->logger;
    }

}
