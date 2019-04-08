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

class StringTypeTest extends TestCase
{

    public function provideDataForEncode()
    {
        return array(
            array("foo", '"foo"'),
            array("fubar", '"fubar"'),
            array('flibble"flobble', '"flibble\"flobble"')
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
     * @covers       \Weasel\JsonMarshaller\Types\StringType
     */
    public function testEncodeString($value, $expected)
    {

        $handler = new StringType();

        $encoded =
            $handler->encodeValue($value,
                $this->_mapper
            );

        $this->assertInternalType("string", $encoded);
        $this->assertEquals($expected, $encoded);

    }

    /**
     * @dataProvider provideDataForEncode
     * @covers       \Weasel\JsonMarshaller\Types\StringType
     */
    public function testDecodeString($value)
    {
        $handler = new StringType();

        $encoded =
            $handler->decodeValue($value,
                $this->_mapper,
                true
            );

        $this->assertInternalType("string", $encoded);
        $this->assertEquals($value, $encoded);
    }

    public function provideBrokenDataForEncode()
    {
        return array(
            array(1.2, '"1.2"'),
            array(7, '"7"'),
            array(false, '""'),
            array(true, '"1"'),
            array(null, '""')
        );
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers       \Weasel\JsonMarshaller\Types\StringType
     */
    public function testNotAStringEncode($value, $expected)
    {
        $handler = new StringType();
        $encoded = $handler->encodeValue($value,
            $this->_mapper
        );
        $this->assertInternalType("string", $encoded);
        $this->assertEquals($expected, $encoded);
    }

    /**
     * @dataProvider provideBrokenDataForEncode
     * @covers       \Weasel\JsonMarshaller\Types\StringType
     * @expectedException \Weasel\JsonMarshaller\Exception\InvalidTypeException
     */
    public function testNotAStringDecode($value)
    {
        $handler = new StringType();
        $handler->decodeValue($value,
            $this->_mapper,
            true
        );
        $this->fail("Should not get here");
    }

}
