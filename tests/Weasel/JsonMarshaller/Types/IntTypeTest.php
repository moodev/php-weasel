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

class IntTypeTest extends TestCase
{

    public function provideDataForEncode()
    {
        return array(
            array(2, '2'),
            array(3, '3'),
            array("123", '123'),
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
     * @covers       \Weasel\JsonMarshaller\Types\IntType
     */
    public function testEncodeInt($value, $expected)
    {

        $handler = new IntType();

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
            array(2, true),
            array(3, true),
            array("123", false),
        );
    }

    /**
     * @dataProvider provideDataForDecode
     * @covers       \Weasel\JsonMarshaller\Types\IntType
     */
    public function testDecodeInt($value, $strict)
    {
        $handler = new IntType();

        $encoded =
            $handler->decodeValue($value,
                $this->_mapper,
                false
            );

        $this->assertInternalType("int", $encoded);
        $this->assertEquals($value, $encoded);

        try {
            $handler->decodeValue($value,
                $this->_mapper,
                true
            );
            if (!$strict) {
                $this->fail("This should not have parsed with strict mode on");
            }
            $this->assertInternalType("int", $encoded);
            $this->assertEquals($value, $encoded);
        } catch (InvalidTypeException $e) {
            if ($strict) {
                $this->fail("This should have parsed with strict mode on");
            }
        }
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
     * @covers       \Weasel\JsonMarshaller\Types\IntType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAIntEncode($value)
    {
        $handler = new IntType();
        $handler->encodeValue($value,
            $this->_mapper
        );
        $this->fail("Should not get here");
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers       \Weasel\JsonMarshaller\Types\IntType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAIntDecode($value)
    {
        $handler = new IntType();
        $handler->decodeValue($value,
            $this->_mapper,
            true
        );
        $this->fail("Should not get here");
    }

}
