<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

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
                "type" => array(),
                "strict" => array()
            ),
            $found
        );

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonProperty
     */
    public function testParseMethodAnnotations()
    {
        $this->markTestSkipped();

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonProperty');
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
                    new \Weasel\Annotation\Config\Annotations\Parameter("name", 'string', false),
                    new \Weasel\Annotation\Config\Annotations\Parameter("type", 'string', false),
                    new \Weasel\Annotation\Config\Annotations\Parameter("strict", 'bool', false),
                )
            )
        )
        ),
            'getName' => array(),
            'getType' => array(),
            'getStrict' => array(),
        );

        $this->assertEquals($expected, $found);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonProperty
     */
    public function testCreate()
    {
        $test = new JsonProperty("foo", "bar", true);
        $this->assertEquals("foo", $test->getName());
        $this->assertEquals("bar", $test->getType());
        $this->assertEquals(true, $test->getStrict());
    }
}
