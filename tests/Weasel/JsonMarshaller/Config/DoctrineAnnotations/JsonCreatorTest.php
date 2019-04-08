<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonCreator;
use Doctrine\Common\Annotations\AnnotationRegistry;

class JsonCreatorTest extends TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonCreator
     */
    public function testBasicClassAnnotations()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonCreator.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getMethodAnnotations(new \ReflectionMethod(__NAMESPACE__ . '\JsonCreatorTestVictim', 'basic'));

        $this->assertEquals(array(
            new JsonCreator(array())
        ),
            $got);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonCreator
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty
     */
    public function testComplexClassAnnotations()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonCreator.php');
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonProperty.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getMethodAnnotations(new \ReflectionMethod(__NAMESPACE__ . '\JsonCreatorTestVictim', 'complex'));

        $this->assertEquals(array(
            new JsonCreator(array("params" => array(
                new JsonProperty(array("name" => "foo", "type" => "int")),
                new JsonProperty(array("name" => "bar", "type" => "int")),
            )))
        ),
            $got);

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonCreator
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty
     */
    public function testConstructorClassAnnotations()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonCreator.php');
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonProperty.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getMethodAnnotations(new \ReflectionMethod(__NAMESPACE__ . '\JsonCreatorTestVictim', '__construct'));

        $this->assertEquals(array(
            new JsonCreator(array("params" => array(
                new JsonProperty(array("name" => "foo", "type" => "int")),
                new JsonProperty(array("name" => "bar", "type" => "int")),
            )))
        ),
            $got);

    }

}

class JsonCreatorTestVictim
{

    /**
     * @JsonCreator
     */
    public static function basic()
    {

    }

    /**
     * @JsonCreator({@JsonProperty(name="foo", type="int"), @JsonProperty(name="bar", type="int")})
     */
    public static function complex()
    {

    }


    /**
     * @JsonCreator(params={@JsonProperty(name="foo", type="int"), @JsonProperty(name="bar", type="int")})
     */
    public function __construct()
    {

    }
}
