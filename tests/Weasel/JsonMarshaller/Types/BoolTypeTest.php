<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

use Weasel\JsonMarshaller\Exception\InvalidTypeException;

require_once(__DIR__ . '/../../../../lib/WeaselAutoloader.php');

class BoolTypeTest extends \PHPUnit_Framework_TestCase
{

    public function provideDataForEncode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 true,
                 false
            )
        );
    }

    /**
     * @dataProvider provideDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\BoolType
     */
    public function testEncodeBool($value)
    {

        $handler = new BoolType();

        $encoded =
            $handler->encodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("bool", $encoded);
        $this->assertEquals($value, $encoded);

    }

    public function provideDataForDecode()
    {
        return array(
            array(true,
                  true
            ),
            array(false,
                  false
            ),
            array("true",
                  true
            ),
            array("false",
                  false
            ),
            array(1,
                  true
            ),
            array(0,
                  false
            )
        );
    }

    /**
     * @dataProvider provideDataForDecode
     * @covers \Weasel\JsonMarshaller\Types\BoolType
     */
    public function testDecodeBool($value, $expected)
    {
        $handler = new BoolType();

        $encoded =
            $handler->decodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("bool", $encoded);
        $this->assertEquals($expected, $encoded);
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
                 7,
                 null
            )
        );
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\BoolType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotABoolEncode($value)
    {
        $handler = new BoolType();
        $handler->encodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\BoolType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotABoolDecode($value)
    {
        $handler = new BoolType();
        $handler->decodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

}
