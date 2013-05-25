<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

use Weasel\JsonMarshaller\Exception\InvalidTypeException;
use Weasel\WeaselDefaultAnnotationDrivenFactory;

class FloatTypeTest extends \PHPUnit_Framework_TestCase
{

    public function provideDataForEncode()
    {
        return array(
            array(2, '2'),
            array(1.2123123123, '1.2123123123'),
            array(3, '3'),
            array(1e8, '100000000'),
            array("123", '123'),
            array("0xaa", '170'),
            array("1e8", '100000000'),
        );
    }

    protected $_mapper;

    protected function setUp()
    {
        parent::setUp();
        $factory = new WeaselDefaultAnnotationDrivenFactory();
        $this->_mapper = $factory->getJsonMapperInstance();
    }

    /**
     * @dataProvider provideDataForEncode
     * @covers \Weasel\JsonMarshaller\Types\FloatType
     */
    public function testEncodeFloat($value, $expected)
    {

        $handler = new FloatType();

        $encoded =
            $handler->encodeValue($value,
                $this->_mapper
            );

        $this->assertInternalType("string", $encoded);
        $this->assertEquals($expected, $encoded);

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
                $this->_mapper
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
            $this->_mapper
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
            $this->_mapper
        );
        $this->fail("Should not get here");
    }

}
