<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;

use PHPUnit\Framework\TestCase;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\WeaselDefaultAnnotationDrivenFactory;

class OldTypeWrapperTest extends TestCase
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
     * @covers       \Weasel\JsonMarshaller\Types\OldTypeWrapper
     */
    public function testEncodeInt($value, $expected)
    {

        $handler = new OldTypeWrapper(new OldIntType());

        $encoded =
            $handler->encodeValue($value,
                $this->_mapper
            );

        $this->assertInternalType("string", $encoded);
        $this->assertEquals($expected, $encoded);

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
     * @covers       \Weasel\JsonMarshaller\Types\OldTypeWrapper
     */
    public function testDecodeInt($value)
    {
        $handler = new OldTypeWrapper(new OldIntType());

        $encoded =
            $handler->decodeValue($value,
                $this->_mapper,
                true
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
     * @covers       \Weasel\JsonMarshaller\Types\OldTypeWrapper
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAIntEncode($value)
    {
        $handler = new OldTypeWrapper(new OldIntType());
        $handler->encodeValue($value,
            $this->_mapper
        );
        $this->fail("Should not get here");
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers       \Weasel\JsonMarshaller\Types\OldTypeWrapper
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAIntDecode($value)
    {
        $handler = new OldTypeWrapper(new OldIntType());
        $handler->decodeValue($value,
            $this->_mapper,
            true
        );
        $this->fail("Should not get here");
    }

}

class OldIntType implements Type
{

    protected function checkAndCastValue($value)
    {
        if (!is_int($value) && !ctype_digit($value)) {
            throw new InvalidTypeException("integer", $value);
        }
        return (int)$value;
    }

    public function decodeValue($value, JsonMapper $mapper)
    {
        return $this->checkAndCastValue($value);
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        return $this->checkAndCastValue($value);
    }

}
