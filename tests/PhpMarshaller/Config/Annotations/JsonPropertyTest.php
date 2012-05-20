<?php
namespace PhpMarshaller\Config\Annotations;

require_once(__DIR__ . '/../../../../PhpMarshallerAutoloader.php');

class JsonPropertyTest extends \PHPUnit_Framework_TestCase
{

    public function testParseClassAnnotations() {

        $annotationReader = new \PhpAnnotation\AnnotationReader(new \ReflectionClass('\PhpMarshaller\Config\Annotations\JsonProperty'), new \PhpAnnotation\AnnotationConfigurator());

        $expected = array(
            '\PhpAnnotation\Annotations\Annotation' => array(
                new \PhpAnnotation\Annotations\Annotation(array("property", "method", '\PhpMarshaller\Config\Annotations\JsonCreator'), null)
            ),
        );

        $this->assertEquals($expected, $annotationReader->getClassAnnotations());

    }

    public function testParsePropertyAnnotations() {

        $rClass = new \ReflectionClass('\PhpMarshaller\Config\Annotations\JsonProperty');
        $annotationReader = new \PhpAnnotation\AnnotationReader($rClass, new \PhpAnnotation\AnnotationConfigurator());


        $found = array();
        foreach ($rClass->getProperties() as $property) {
            $name = $property->getName();
            $found[$name] = $annotationReader->getPropertyAnnotations($name);
        }

        $this->assertEquals(array("name" => array(), "type" => array()), $found);

    }

    public function testParseMethodAnnotations() {

        $rClass = new \ReflectionClass('\PhpMarshaller\Config\Annotations\JsonProperty');
        $annotationReader = new \PhpAnnotation\AnnotationReader($rClass, new \PhpAnnotation\AnnotationConfigurator());

        $found = array();
        foreach ($rClass->getMethods() as $method) {
            $name = $method->getName();
            $found[$name] = $annotationReader->getMethodAnnotations($name);
        }

        $expected = array("__construct" =>
            array('\PhpAnnotation\Annotations\AnnotationCreator' => array(
                new \PhpAnnotation\Annotations\AnnotationCreator(
                    array(
                        new \PhpAnnotation\Annotations\Parameter("name", 'string', false),
                        new \PhpAnnotation\Annotations\Parameter("type", 'string', false),
                    )
                )
            )),
            'getName' => array(),
            'getType' => array(),
        );

        $this->assertEquals($expected, $found);

    }
}
