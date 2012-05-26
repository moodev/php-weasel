<?php
namespace PhpAnnotation;


class AnnotationConfigurator
{

     protected $builtIn = array(
        '\PhpAnnotation\Annotations\Annotation' => array(
            'class' => '\PhpAnnotation\Annotations\Annotation',
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
        '\PhpAnnotation\Annotations\AnnotationCreator' => array(
            'class' => '\PhpAnnotation\Annotations\AnnotationCreator',
            'on' => array('method'),
            'max' => 1,
            'creatorMethod' => '__construct',
            'creatorParams' => array(
                array(
                    'name' => 'params',
                    'type' => '\PhpAnnotation\Annotations\Parameter[]',
                    'required' => false
                ),
            ),
        ),
        '\PhpAnnotation\Annotations\Parameter' => array(
            'class' => '\PhpAnnotation\Annotations\Parameter',
            'on' => array('\PhpAnnotation\Annotations\AnnotationCreator'),
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

    public function get($name)
    {
        if (isset($this->builtIn[$name])) {
            return $this->builtIn[$name];
        }

        $class = new \ReflectionClass($name);
        $reader = new AnnotationReader($class, $this);

        /**
         * @var \PhpAnnotation\Annotations\Annotation $annotation
         */
        $annotation = $reader->getSingleClassAnnotation('\PhpAnnotation\Annotations\Annotation');
        if (!isset($annotation)) {
            throw new \Exception("erm");
        }

        $metaConfig = array();
        $metaConfig['class'] = $name;
        $metaConfig['on'] = $annotation->getOn();
        $metaConfig['max'] = $annotation->getMax();

        foreach ($class->getMethods() as $method) {
            /**
             * @var \ReflectionMethod $method
             * @var \PhpAnnotation\Annotations\AnnotationCreator $creator
             */
            $creator = $reader->getSingleMethodAnnotation($method->getName(), '\PhpAnnotation\Annotations\AnnotationCreator');
            if (isset($creator)) {
                $metaConfig['creatorMethod'] = $method->getName();
                $creatorParams = array();
                foreach ($creator->getParams() as $param) {
                    $creatorParam = array();
                    $creatorParam['name'] = $param->getName();
                    $creatorParam['type'] = $param->getType();
                    $creatorParam['required'] = $param->getRequired();
                    $creatorParams[] = $creatorParam;
                }
                $metaConfig['creatorParams'] = $creatorParams;

            }
        }

        $metaConfig['properties'] = array();
        foreach ($class->getProperties() as $property) {
            /**
             * @var \ReflectionProperty $property
             * @var \PhpAnnotation\Annotations\Property $annotProperty
             */
            $annotProperty = $reader->getSinglePropertyAnnotation($property->getName(), '\PhpAnnotation\Annotations\Property');

            if (isset($annotProperty)) {
                $propertyConfig = array();
                $propertyConfig['type'] = $annotProperty->getType();
                $metaConfig['properties'][$property->getName()] = $propertyConfig;
            }

            /**
             * @var \PhpAnnotation\Annotations\Enum $annotEnum
             */
            $annotEnum = $reader->getSinglePropertyAnnotation($property->getName(), '\PhpAnnotation\Annotations\Enum');

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

}
