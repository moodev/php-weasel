<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonIgnoreProperties;
use Doctrine\Common\Annotations\AnnotationRegistry;

class JsonIgnorePropertiesTest extends TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonIgnoreProperties
     */
    public function testBasicUsage()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonIgnoreProperties.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getClassAnnotation(new \ReflectionClass(__NAMESPACE__ . '\JsonIgnorePropertiesTestVictimA'),
            __NAMESPACE__ . '\JsonIgnoreProperties');

        $this->assertInstanceOf(__NAMESPACE__ . '\JsonIgnoreProperties', $got);
        $this->assertEquals(array("blork"), $got->getNames());

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonIgnoreProperties
     */
    public function testListUsage()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonIgnoreProperties.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getClassAnnotation(new \ReflectionClass(__NAMESPACE__ . '\JsonIgnorePropertiesTestVictimB'),
            __NAMESPACE__ . '\JsonIgnoreProperties');

        $this->assertInstanceOf(__NAMESPACE__ . '\JsonIgnoreProperties', $got);
        $this->assertEquals(array("blork", "blark"), $got->getNames());

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonIgnoreProperties
     */
    public function testNamedListUsage()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonIgnoreProperties.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getClassAnnotation(new \ReflectionClass(__NAMESPACE__ . '\JsonIgnorePropertiesTestVictimC'),
            __NAMESPACE__ . '\JsonIgnoreProperties');

        $this->assertInstanceOf(__NAMESPACE__ . '\JsonIgnoreProperties', $got);
        $this->assertEquals(array("blork", "blark"), $got->getNames());

    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonIgnoreProperties
     */
    public function testIgnoreUsage()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonIgnoreProperties.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getClassAnnotation(new \ReflectionClass(__NAMESPACE__ . '\JsonIgnorePropertiesTestVictimD'),
            __NAMESPACE__ . '\JsonIgnoreProperties');

        $this->assertInstanceOf(__NAMESPACE__ . '\JsonIgnoreProperties', $got);
        $this->assertEquals(array(), $got->getNames());
        $this->assertTrue($got->getIgnoreUnknown());

    }
}

/**
 * @JsonIgnoreProperties("blork")
 */
class JsonIgnorePropertiesTestVictimA
{

}

/**
 * @JsonIgnoreProperties({"blork", "blark"})
 */
class JsonIgnorePropertiesTestVictimB
{

}

/**
 * @JsonIgnoreProperties(names={"blork", "blark"})
 */
class JsonIgnorePropertiesTestVictimC
{

}


/**
 * @JsonIgnoreProperties(ignoreUnknown=true)
 */
class JsonIgnorePropertiesTestVictimD
{

}

