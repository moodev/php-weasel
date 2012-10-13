<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

use Weasel\JsonMarshaller\Exception\InvalidTypeException;

require_once(__DIR__ . '/../../../../lib/WeaselAutoloader.php');

class StringTypeTest extends \PHPUnit_Framework_TestCase
{

    public function provideDataForEncode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 "foo",
                 "fubar"
            )
        );
    }

    /**
     * @dataProvider provideDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\StringType
     */
    public function testEncodeString($value)
    {

        $handler = new StringType();

        $encoded =
            $handler->encodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("string", $encoded);
        $this->assertEquals($value, $encoded);

    }

    /**
     * @dataProvider provideDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\StringType
     */
    public function testDecodeString($value)
    {
        $handler = new StringType();

        $encoded =
            $handler->decodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("string", $encoded);
        $this->assertEquals($value, $encoded);
    }

    public function provideBrokenDataForEncode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 1.2,
                 7,
                 false,
                 true,
                 null
            )
        );
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\StringType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAStringEncode($value)
    {
        $handler = new StringType();
        $handler->encodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\StringType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAStringDecode($value)
    {
        $handler = new StringType();
        $handler->decodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

}
