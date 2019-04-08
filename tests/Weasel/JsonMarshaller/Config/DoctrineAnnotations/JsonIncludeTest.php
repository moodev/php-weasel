<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonInclude;
use Doctrine\Common\Annotations\AnnotationRegistry;

class JsonIncludeTest extends TestCase
{
    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonInclude
     */
    public function testBasic()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonInclude.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getClassAnnotation(new \ReflectionClass(__NAMESPACE__ . '\JsonIncludeTestVictim'),
            __NAMESPACE__ . '\JsonInclude');

        $this->assertInstanceOf(__NAMESPACE__ . '\JsonInclude', $got);
        $this->assertEquals(JsonInclude::INCLUDE_ALWAYS, $got->getValue());

    }
}

/**
 * @JsonInclude(JsonInclude::INCLUDE_ALWAYS)
 */
class JsonIncludeTestVictim
{

}
