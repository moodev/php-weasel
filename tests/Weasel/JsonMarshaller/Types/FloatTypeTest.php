<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

use Weasel\JsonMarshaller\Exception\InvalidTypeException;

require_once(__DIR__ . '/../../../../lib/WeaselAutoloader.php');

class FloatTypeTest extends \PHPUnit_Framework_TestCase
{

    public function provideDataForEncode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 2,
                 1.2123123123123,
                 3,
                 1e8,
                 "123",
                 "0xaa",
                 "1e8"
            )
        );
    }

    /**
     * @dataProvider provideDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\FloatType
     */
    public function testEncodeFloat($value)
    {

        $handler = new FloatType();

        $encoded =
            $handler->encodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("float", $encoded);
        $this->assertEquals($value, $encoded);

    }

    /**
     * @dataProvider provideDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\FloatType
     */
    public function testDecodeFloat($value)
    {
        $handler = new FloatType();

        $encoded =
            $handler->decodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("float", $encoded);
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
                 "0xzz"
            )
        );
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\FloatType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAFloatEncode($value)
    {
        $handler = new FloatType();
        $handler->encodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\FloatType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAFloatDecode($value)
    {
        $handler = new FloatType();
        $handler->decodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

}
