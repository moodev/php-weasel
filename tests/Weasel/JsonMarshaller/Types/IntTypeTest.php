<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

use Weasel\JsonMarshaller\Exception\InvalidTypeException;

require_once(__DIR__ . '/../../../../lib/WeaselAutoloader.php');

class IntTypeTest extends \PHPUnit_Framework_TestCase
{

    public function provideDataForEncode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 2,
                 3,
                 "123",
            )
        );
    }

    /**
     * @dataProvider provideDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\IntType
     */
    public function testEncodeInt($value)
    {

        $handler = new IntType();

        $encoded =
            $handler->encodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("int", $encoded);
        $this->assertEquals($value, $encoded);

    }

    public function provideDataForDecode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 2,
                 3,
                 "123",
            )
        );
    }

    /**
     * @dataProvider provideDataForDecode
     * @covers \Weasel\JsonMarshaller\Types\IntType
     */
    public function testDecodeInt($value)
    {
        $handler = new IntType();

        $encoded =
            $handler->decodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("int", $encoded);
        $this->assertEquals($value, $encoded);
    }

    public function provideBrokenDataForEncode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 "hi mum!",
                 "f00ff0f0abc",
                 "0xzz",
                 1.2
            )
        );
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\IntType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAIntEncode($value)
    {
        $handler = new IntType();
        $handler->encodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\IntType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAIntDecode($value)
    {
        $handler = new IntType();
        $handler->decodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

}
