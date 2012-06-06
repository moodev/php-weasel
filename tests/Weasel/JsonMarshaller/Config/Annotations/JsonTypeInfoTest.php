<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

require_once(__DIR__ . '/../../../../../lib/WeaselAutoloader.php');

class JsonTypeInfoTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonTypeInfo
     */
    public function testParseClassAnnotations()
    {

        $annotationReader =
            new \Weasel\Annotation\AnnotationReader(new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonTypeInfo'), new \Weasel\Annotation\AnnotationConfigurator());

        $expected = array(
            '\Weasel\Annotation\Config\Annotations\Annotation' => array(new \Weasel\Annotation\Config\Annotations\Annotation(array("class",
                                                                                                                                   "method",
                                                                                                                                   "property"
                                                                                                                             ), null)
            ),
        );

        $this->assertEquals($expected, $annotationReader->getClassAnnotations());

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonTypeInfo
     */
    public function testParsePropertyAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonTypeInfo');
        $annotationReader =
            new \Weasel\Annotation\AnnotationReader($rClass, new \Weasel\Annotation\AnnotationConfigurator());


        $found = array();
        foreach ($rClass->getProperties() as $property) {
            $name = $property->getName();
            $found[$name] = $annotationReader->getPropertyAnnotations($name);
        }

        $expectedEnumId = new \Weasel\Annotation\Config\Annotations\Enum("Id");
        $expectedEnumAs = new \Weasel\Annotation\Config\Annotations\Enum("As");

        $this->assertEquals(array("enumId" => array('\Weasel\Annotation\Config\Annotations\Enum' => array($expectedEnumId),),
                                  "enumAs" => array('\Weasel\Annotation\Config\Annotations\Enum' => array($expectedEnumAs),),
                                  "use" => array(),
                                  "include" => array(),
                                  "property" => array(),
                                  "visible" => array(),
                                  "defaultImpl" => array()
                            ), $found
        );

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonTypeInfo
     */
    public function testParseMethodAnnotations()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\Annotations\JsonTypeInfo');
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
                                       new \Weasel\Annotation\Config\Annotations\Parameter("use", 'integer', true),
                                       new \Weasel\Annotation\Config\Annotations\Parameter("include", 'integer', false),
                                       new \Weasel\Annotation\Config\Annotations\Parameter("property", 'string', false),
                                       new \Weasel\Annotation\Config\Annotations\Parameter("visible", 'bool', false),
                                       new \Weasel\Annotation\Config\Annotations\Parameter("defaultImpl", 'string', false),
                                  )
                              )
                          )
                          ),
                          "getUse" => array(),
                          "getInclude" => array(),
                          "getProperty" => array(),
                          "getVisible" => array(),
                          "getDefaultImpl" => array(),

        );

        $this->assertEquals($expected, $found);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\Annotations\JsonTypeInfo
     */
    public function testCreate()
    {
        $test =
            new JsonTypeInfo(JsonTypeInfo::$enumAs["PROPERTY"], JsonTypeInfo::$enumId["NAME"], "testProp", true, "FooBar");
        $this->assertEquals(JsonTypeInfo::$enumAs["PROPERTY"], $test->getUse());
        $this->assertEquals(JsonTypeInfo::$enumId["NAME"], $test->getInclude());
        $this->assertEquals("testProp", $test->getProperty());
        $this->assertEquals(true, $test->getVisible());
        $this->assertEquals("FooBar", $test->getDefaultImpl());
    }
}
