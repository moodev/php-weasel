<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;
use Exception;
use DateTime;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

class DateTimeType implements Type
{

    protected $dateTimeFormat;

    public function __construct($dateTimeFormat = DateTime::ISO8601)
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    public function decodeValue($value, JsonMapper $mapper)
    {
        if (!is_string($value)) {
            throw new InvalidTypeException('date string', $value);
        }
        try {
            $retval = DateTime::createFromFormat($this->dateTimeFormat, $value);
            if ($retval !== false) {
                return $retval;
            }
        } catch (Exception $e) {
            throw new InvalidTypeException('date string', $value, 0, $e);
        }
        throw new InvalidTypeException('date string', $value);
    }

    /**
     * @param \DateTime $value
     * @param \Weasel\JsonMarshaller\JsonMapper $mapper
     * @throws \Weasel\JsonMarshaller\Exception\InvalidTypeException
     * @return string
     */
    public function encodeValue($value, JsonMapper $mapper)
    {
        if (!$value instanceof DateTime) {
            throw new InvalidTypeException('\DateTime', $value);
        }
        return $value->format($this->dateTimeFormat);
    }

}
