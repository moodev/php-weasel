<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

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
                 "123"
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

}
