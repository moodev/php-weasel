<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

require_once(__DIR__ . '/../../../../../lib/WeaselAutoloader.php');

class JsonPropertyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonProperty
     */
    public function testParseClassAnnotations()
    {

        $annotationReader =
            new \Weasel\Annotation\AnnotationReader(new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonProperty'), new \Weasel\Annotation\AnnotationConfigurator());

        $expected = array(
            '\Weasel\Annotation\Config\Annotations\Annotation' => array(
                new \Weasel\Annotation\Config\Annotations\Annotation(array("property",
                                                                           "method",
                                                                           '\Weasel\JsonMarshaller\Config\Annotations\JsonCreator'
                                                                     ), null)
            ),
        );

        $this->assertEquals($expected, $annotationReader->getClassAnnotations());

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonProperty
     */
    public function testParsePropertyAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonProperty');
        $annotationReader =
            new \Weasel\Annotation\AnnotationReader($rClass, new \Weasel\Annotation\AnnotationConfigurator());


        $found = array();
        foreach ($rClass->getProperties() as $property) {
            $name = $property->getName();
            $found[$name] = $annotationReader->getPropertyAnnotations($name);
        }

        $this->assertEquals(array("name" => array(),
                                  "type" => array()
                            ), $found
        );

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonProperty
     */
    public function testParseMethodAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonProperty');
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
                                       new \Weasel\Annotation\Config\Annotations\Parameter("name", 'string', false),
                                       new \Weasel\Annotation\Config\Annotations\Parameter("type", 'string', false),
                                  )
                              )
                          )
                          ),
                          'getName' => array(),
                          'getType' => array(),
        );

        $this->assertEquals($expected, $found);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonProperty
     */
    public function testCreate()
    {
        $test = new JsonProperty("foo", "bar");
        $this->assertEquals("foo", $test->getName());
        $this->assertEquals("bar", $test->getType());
    }
}
