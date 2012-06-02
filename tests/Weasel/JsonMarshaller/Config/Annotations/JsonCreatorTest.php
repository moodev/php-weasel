<?php
namespace Weasel\JsonMarshaller\Config\Annotations;

require_once(__DIR__ . '/../../../../../WeaselAutoloader.php');

class JsonCreatorTest extends \PHPUnit_Framework_TestCase
{

    public function testParseClassAnnotations() {

        $annotationReader = new \Weasel\Annotation\AnnotationReader(new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonCreator'), new \Weasel\Annotation\AnnotationConfigurator());

        $expected = array(
            '\Weasel\Annotation\Annotations\Annotation' => array(new \Weasel\Annotation\Annotations\Annotation(array("method"), 1)),
        );

        $this->assertEquals($expected, $annotationReader->getClassAnnotations());

    }

    public function testParsePropertyAnnotations() {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonCreator');
        $annotationReader = new \Weasel\Annotation\AnnotationReader($rClass, new \Weasel\Annotation\AnnotationConfigurator());


        $found = array();
        foreach ($rClass->getProperties() as $property) {
            $name = $property->getName();
            $found[$name] = $annotationReader->getPropertyAnnotations($name);
        }

        $this->assertEquals(array("params" => array()), $found);

    }

    public function testParseMethodAnnotations() {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonCreator');
        $annotationReader = new \Weasel\Annotation\AnnotationReader($rClass, new \Weasel\Annotation\AnnotationConfigurator());

        $found = array();
        foreach ($rClass->getMethods() as $method) {
            /**
             * @var \ReflectionMethod $method
             */
            $name = $method->getName();
            $found[$name] = $annotationReader->getMethodAnnotations($name);
        }

        $expected = array("__construct" =>
            array('\Weasel\Annotation\Annotations\AnnotationCreator' => array(
                new \Weasel\Annotation\Annotations\AnnotationCreator(
                    array(
                        new \Weasel\Annotation\Annotations\Parameter("params", '\Weasel\JsonMarshaller\Config\Annotations\JsonProperty[]', false),
                    )
                )
            )),
            "getParams" => array(),
        );

        $this->assertEquals($expected, $found);

    }
}
