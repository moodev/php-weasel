<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

use PHPUnit\Framework\TestCase;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;
use Weasel\WeaselDefaultAnnotationDrivenFactory;

class BoolTypeTest extends TestCase
{


    public function provideDataForEncode()
    {
        return array(
            array(true, "true"),
            array(false, "false")
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
     * @covers       \Weasel\JsonMarshaller\Types\BoolType
     */
    public function testEncodeBool($value, $expected)
    {

        $handler = new BoolType();

        $encoded =
            $handler->encodeValue($value,
                $this->_mapper
            );

        $this->assertInternalType("string", $encoded);
        $this->assertEquals($expected, $encoded);

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
     * @covers       \Weasel\JsonMarshaller\Types\BoolType
     */
    public function testDecodeBool($value, $expected)
    {
        $handler = new BoolType();

        $encoded =
            $handler->decodeValue($value,
                $this->_mapper,
                true
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
     * @covers       \Weasel\JsonMarshaller\Types\BoolType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotABoolEncode($value)
    {
        $handler = new BoolType();
        $handler->encodeValue($value,
            $this->_mapper
        );
        $this->fail("Should not get here");
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers       \Weasel\JsonMarshaller\Types\BoolType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotABoolDecode($value)
    {
        $handler = new BoolType();
        $handler->decodeValue($value,
            $this->_mapper,
            true
        );
        $this->fail("Should not get here");
    }

}
