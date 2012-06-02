<?php
namespace PhpAnnotation;


class AnnotationConfigurator
{

     protected $builtIn = array(
        '\Annotation\Annotations\Annotation' => array(
            'class' => '\Annotation\Annotations\Annotation',
            'on' => array('class'),
            'max' => 1,
            'creatorMethod' => '__construct',
            'creatorParams' => array(
                array(
                    'name' => 'on',
                    'type' => 'string[]',
                    'required' => false
                ),
                array(
                    'name' => 'max',
                    'type' => 'integer',
                    'required' => false
                ),
            ),
        ),
        '\Annotation\Annotations\AnnotationCreator' => array(
            'class' => '\Annotation\Annotations\AnnotationCreator',
            'on' => array('method'),
            'max' => 1,
            'creatorMethod' => '__construct',
            'creatorParams' => array(
                array(
                    'name' => 'params',
                    'type' => '\Annotation\Annotations\Parameter[]',
                    'required' => false
                ),
            ),
        ),
        '\Annotation\Annotations\Parameter' => array(
            'class' => '\Annotation\Annotations\Parameter',
            'on' => array('\Annotation\Annotations\AnnotationCreator'),
            'max' => null,
            'creatorMethod' => '__construct',
            'creatorParams' => array(
                array(
                    'name' => 'name',
                    'type' => 'string',
                    'required' => false
                ),
                array(
                    'name' => 'type',
                    'type' => 'string',
                    'required' => false
                ),
                array(
                    'name' => 'required',
                    'type' => 'boolean',
                    'required' => false
                ),
            ),
        ),

    );

    protected $logger;

    public function __construct(\PhpLogger\Logger $logger = null) {
        $this->logger = $logger;
    }

    public function get($name)
    {
        if (isset($this->builtIn[$name])) {
            return $this->builtIn[$name];
        }

        $class = new \ReflectionClass($name);
        $reader = new AnnotationReader($class, $this);

        /**
         * @var \Annotation\Annotations\Annotation $annotation
         */
        $annotation = $reader->getSingleClassAnnotation('\Annotation\Annotations\Annotation');
        if (!isset($annotation)) {
            throw new \Exception("Did not find an @Annotation annotation on $name");
        }

        $metaConfig = array();
        $metaConfig['class'] = $name;
        $metaConfig['on'] = $annotation->getOn();
        $metaConfig['max'] = $annotation->getMax();

        foreach ($class->getMethods() as $method) {
            /**
             * @var \ReflectionMethod $method
             * @var \Annotation\Annotations\AnnotationCreator $creator
             */
            $creator = $reader->getSingleMethodAnnotation($method->getName(), '\Annotation\Annotations\AnnotationCreator');
            if (isset($creator)) {
                $metaConfig['creatorMethod'] = $method->getName();
                $creatorArgs = $method->getParameters();
                if (count($creatorArgs) != count($creator->getParams())) {
                    throw new \Exception("Creator args don't match with method args");
                }
                $creatorParams = array();
                $i = 0;
                foreach ($creator->getParams() as $param) {
                    $creatorParam = array();
                    $creatorParam['name'] = $param->getName();
                    if (!isset($creatorParam['name'])) {
                        $creatorParam['name'] = $creatorArgs[$i]->getName();
                    }
                    $creatorParam['type'] = $param->getType();
                    $creatorParam['required'] = $param->getRequired();
                    $creatorParams[] = $creatorParam;
                }
                $i++;
                $metaConfig['creatorParams'] = $creatorParams;

            }
        }

        $metaConfig['properties'] = array();
        foreach ($class->getProperties() as $property) {
            /**
             * @var \ReflectionProperty $property
             * @var \Annotation\Annotations\Property $annotProperty
             */
            $annotProperty = $reader->getSinglePropertyAnnotation($property->getName(), '\Annotation\Annotations\Property');

            if (isset($annotProperty)) {
                $propertyConfig = array();
                $propertyConfig['type'] = $annotProperty->getType();
                $metaConfig['properties'][$property->getName()] = $propertyConfig;
            }

            /**
             * @var \Annotation\Annotations\Enum $annotEnum
             */
            $annotEnum = $reader->getSinglePropertyAnnotation($property->getName(), '\Annotation\Annotations\Enum');

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
                $metaConfig['enums'][$name] = $value;
            }

        }

        return $metaConfig;
    }

    public function getLogger()
    {
        return $this->logger;
    }

}
