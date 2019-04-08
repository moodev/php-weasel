<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

use PHPUnit\Framework\TestCase;

class JsonTypeNameTest extends TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonTypeName
     */
    public function testParseClassAnnotations()
    {

        $annotationReader =
            new \Weasel\Annotation\AnnotationReader(new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonTypeName'), new \Weasel\Annotation\AnnotationConfigurator());

        $expected = array(
            '\Weasel\Annotation\Config\Annotations\Annotation' => array(new \Weasel\Annotation\Config\Annotations\Annotation(array("class"), null)),
        );

        $this->assertEquals($expected, $annotationReader->getClassAnnotations());

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonTypeName
     */
    public function testParsePropertyAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonTypeName');
        $annotationReader =
            new \Weasel\Annotation\AnnotationReader($rClass, new \Weasel\Annotation\AnnotationConfigurator());


        $found = array();
        foreach ($rClass->getProperties() as $property) {
            $name = $property->getName();
            $found[$name] = $annotationReader->getPropertyAnnotations($name);
        }

        $this->assertEquals(array("name" => array()), $found);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonTypeName
     */
    public function testParseMethodAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonTypeName');
        $annotationReader =
            new \Weasel\Annotation\AnnotationReader($rClass, new \Weasel\Annotation\AnnotationConfigurator());

        $found = array();
        foreach ($rClass->getMethods() as $method) {
            /**
             * @var \ReflectionMethod $method
             */
            $name = $method->getName();
            if (substr($name, 0, 2) === '__' && !($method->isStatic() || $method->isConstructor())) {
                continue;
            }
            $found[$name] = $annotationReader->getMethodAnnotations($name);
        }

        $expected = array("__construct" =>
        array('\Weasel\Annotation\Config\Annotations\AnnotationCreator' => array(
            new \Weasel\Annotation\Config\Annotations\AnnotationCreator(
                array(
                    new \Weasel\Annotation\Config\Annotations\Parameter("name", 'string', true),
                )
            )
        )
        ),
            "getName" => array(),
        );

        $this->assertEquals($expected, $found);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonTypeName
     */
    public function testCreate()
    {
        $test = new JsonTypeName("testName");
        $this->assertEquals("testName", $test->getName());
    }
}
