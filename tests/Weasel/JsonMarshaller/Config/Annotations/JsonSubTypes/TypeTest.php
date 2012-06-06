<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes;

require_once(__DIR__ . '/../../../../../../lib/WeaselAutoloader.php');

class TypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type
     */
    public function testParseClassAnnotations()
    {

        $annotationReader =
            new \Weasel\Annotation\AnnotationReader(new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type'), new \Weasel\Annotation\AnnotationConfigurator());

        $expected = array(
            '\Weasel\Annotation\Config\Annotations\Annotation' => array(new \Weasel\Annotation\Config\Annotations\Annotation(array('\Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes'), null)),
        );

        $this->assertEquals($expected, $annotationReader->getClassAnnotations());

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type
     */
    public function testParsePropertyAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type');
        $annotationReader =
            new \Weasel\Annotation\AnnotationReader($rClass, new \Weasel\Annotation\AnnotationConfigurator());


        $found = array();
        foreach ($rClass->getProperties() as $property) {
            $name = $property->getName();
            $found[$name] = $annotationReader->getPropertyAnnotations($name);
        }

        $this->assertEquals(array("value" => array(),
                                  "name" => array()
                            ), $found
        );

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type
     */
    public function testParseMethodAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type');
        $annotationReader =
            new \Weasel\Annotation\AnnotationReader($rClass, new \Weasel\Annotation\AnnotationConfigurator());

        $found = array();
        foreach ($rClass->getMethods() as $method) {
            /**
             * @var \ReflectionMethod $method
             */
            $name = $method->getName();
            $found[$name] = $annotationReader->getMethodAnnotations($name);
        }

        $expected = array("__construct" =>
                          array('\Weasel\Annotation\Config\Annotations\AnnotationCreator' => array(
                              new \Weasel\Annotation\Config\Annotations\AnnotationCreator(
                                  array(
                                       new \Weasel\Annotation\Config\Annotations\Parameter("value", 'string', true),
                                       new \Weasel\Annotation\Config\Annotations\Parameter("name", 'string', false),
                                  )
                              )
                          )
                          ),
                          "getValue" => array(),
                          "getName" => array(),
        );

        $this->assertEquals($expected, $found);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type
     */
    public function testCreate()
    {
        $test = new Type("testValue", "testName");
        $this->assertEquals("testValue", $test->getValue());
        $this->assertEquals("testName", $test->getName());
    }
}
