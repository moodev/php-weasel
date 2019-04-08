<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonAnyGetter;
use Doctrine\Common\Annotations\AnnotationRegistry;

class JsonAnyGetterTest extends TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonAnyGetter
     */
    public function testBasicClassAnnotations()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../../../../../lib/Weasel/JsonMarshaller/Config/DoctrineAnnotations/JsonAnyGetter.php');

        $annotationReader = new AnnotationReader();
        $got = $annotationReader->getMethodAnnotations(new \ReflectionMethod(__NAMESPACE__ . '\JsonAnyGetterTestVictim', 'basic'));

        $this->assertEquals(array(
            new JsonAnyGetter()
        ),
            $got);

    }

}

class JsonAnyGetterTestVictim
{

    /**
     * @JsonAnyGetter
     */
    public static function basic()
    {

    }
}
