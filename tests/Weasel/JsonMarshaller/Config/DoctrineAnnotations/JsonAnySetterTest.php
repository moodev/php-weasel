<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonAnySetter;
use Doctrine\Common\Annotations\AnnotationRegistry;

class JsonAnySetterTest extends TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonAnySetter
     */
    public function testBasicClassAnnotations()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonAnySetter.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getMethodAnnotations(new \ReflectionMethod(__NAMESPACE__ . '\JsonAnySetterTestVictim', 'basic'));

        $this->assertEquals(array(
            new JsonAnySetter()
        ),
            $got);

    }

}

class JsonAnySetterTestVictim
{

    /**
     * @JsonAnySetter
     */
    public static function basic()
    {

    }
}
