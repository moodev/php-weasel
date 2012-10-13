<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

use Weasel\JsonMarshaller\Exception\InvalidTypeException;

require_once(__DIR__ . '/../../../../lib/WeaselAutoloader.php');

class DateTimeTypeTest extends \PHPUnit_Framework_TestCase
{

    public function provideDataForEncode()
    {
        return array(
            array(new \DateTime("2012-11-12 12:12:12", new \DateTimeZone("UTC")),
                  "2012-11-12T12:12:12+0000"
            ),
        );
    }

    /**
     * @dataProvider provideDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\DateTimeType
     */
    public function testEncodeDateTime($value, $expected)
    {

        $handler = new DateTimeType();

        $encoded =
            $handler->encodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInternalType("string", $encoded);
        $this->assertEquals($expected, $encoded);

    }

    public function provideDataForDecode()
    {
        return array(
            array("2012-11-12T12:12:12+0000",
                  new \DateTime("2012-11-12 12:12:12", new \DateTimeZone("UTC"))
            ),
        );
    }

    /**
     * @dataProvider provideDataForDecode
     * @covers \Weasel\JsonMarshaller\Types\DateTimeType
     * @param string $value
     * @param \DateTime $expected
     * @return void
     */
    public function testDecodeDateTime($value, \DateTime $expected)
    {
        $handler = new DateTimeType();

        $encoded =
            $handler->decodeValue($value,
                                  new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
            );

        $this->assertInstanceOf('\DateTime', $encoded);
        $this->assertEquals($expected->format("U"), $encoded->format("U"));
    }

    public function provideBrokenDataForEncode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 null,
                 1,
                 "foo"
            )
        );
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\DateTimeType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotADateTimeEncode($value)
    {
        $handler = new DateTimeType();
        $handler->encodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

    public function provideBrokenDataForDecode()
    {
        return array_map(function ($a) {
                return array($a);
            },
            array(
                 null,
                 1,
                 "foo",
                 "2012-01-02 11:11:11"
            )
        );
    }

    /**
     * @dataProvider provideBrokenDataForDecode
     * @covers \Weasel\JsonMarshaller\Types\DateTimeType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotADateTimeDecode($value)
    {
        $handler = new DateTimeType();
        $handler->decodeValue($value,
                              new \Weasel\JsonMarshaller\JsonMapper(new \Weasel\JsonMarshaller\Config\AnnotationDriver())
        );
        $this->fail("Should not get here");
    }

}
