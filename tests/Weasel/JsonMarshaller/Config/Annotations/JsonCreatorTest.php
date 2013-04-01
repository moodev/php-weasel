<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

use Weasel\Annotation\AnnotationReader;
use Weasel\Annotation\AnnotationConfigurator;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;

class JsonCreatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonCreator
     */
    public function testParseClassAnnotations()
    {

        $annotationReader =
            new AnnotationReader(new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonCreator'), new AnnotationConfigurator());

        $expected = array(
            '\Weasel\Annotation\Config\Annotations\Annotation' => array(new \Weasel\Annotation\Config\Annotations\Annotation(array("method"), 1)),
        );

        $this->assertEquals($expected, $annotationReader->getClassAnnotations());

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonCreator
     */
    public function testParsePropertyAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonCreator');
        $annotationReader =
            new AnnotationReader($rClass, new AnnotationConfigurator());


        $found = array();
        foreach ($rClass->getProperties() as $property) {
            $name = $property->getName();
            $found[$name] = $annotationReader->getPropertyAnnotations($name);
        }

        $this->assertEquals(array("params" => array()), $found);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonCreator
     */
    public function testParseMethodAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonCreator');
        $annotationReader =
            new AnnotationReader($rClass, new AnnotationConfigurator());

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
            new AnnotationCreator(
                array(
                    new Parameter("params", '\Weasel\JsonMarshaller\Config\Annotations\JsonProperty[]', false),
                )
            )
        )
        ),
            "getParams" => array(),
        );

        $this->assertEquals($expected, $found);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonCreator
     */
    public function testCreate()
    {
        $test = new JsonCreator(array());
        $this->assertEmpty($test->getParams());
    }
}
