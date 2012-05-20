<?php
namespace PhpMarshaller\Config\Annotations;

require_once(__DIR__ . '/../../../../PhpMarshallerAutoloader.php');

class JsonCreatorTest extends \PHPUnit_Framework_TestCase
{

    public function testParseClassAnnotations() {

        $annotationReader = new \PhpAnnotation\AnnotationReader(new \ReflectionClass('\PhpMarshaller\Config\Annotations\JsonCreator'), new \PhpAnnotation\AnnotationConfigurator());

        $expected = array(
            '\PhpAnnotation\Annotations\Annotation' => array(new \PhpAnnotation\Annotations\Annotation(array("method"), 1)),
        );

        $this->assertEquals($expected, $annotationReader->getClassAnnotations());

    }

    public function testParsePropertyAnnotations() {

        $rClass = new \ReflectionClass('\PhpMarshaller\Config\Annotations\JsonCreator');
        $annotationReader = new \PhpAnnotation\AnnotationReader($rClass, new \PhpAnnotation\AnnotationConfigurator());


        $found = array();
        foreach ($rClass->getProperties() as $property) {
            $name = $property->getName();
            $found[$name] = $annotationReader->getPropertyAnnotations($name);
        }

        $this->assertEquals(array("params" => array()), $found);

    }

    public function testParseMethodAnnotations() {

        $rClass = new \ReflectionClass('\PhpMarshaller\Config\Annotations\JsonCreator');
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
                        new \PhpAnnotation\Annotations\Parameter("params", '\PhpMarshaller\Config\Annotations\JsonProperty', false),
                    )
                )
            ))
        );

        $this->assertEquals($expected, $found);

    }
}
