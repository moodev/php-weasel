<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

/**
 * Features for config general mapper behaviour.
 * These are the keys to be passed to the mapper configure() method.
 */
interface Feature {
    /**
     * bool. When true,  throw an exception (UnknownPropertyException) on encountering an unknown property in the JSON and
     * there's no AnySetter configured (or ignoreUnknown isn't set.) Default: false.
     */
    const FAIL_ON_UNKNOWN_PROPERTIES = "deserialization::fail_on_unknown";
    /**
     * bool. When true trigger an E_USER_WARNING on encountering an unknown property in the JSON and there's no AnySetter
     * configured (or ignoreUnknown isn't set.) Default: true.
     */
    const WARN_ON_UNKNOWN_PROPERTIES = "deserialization::warn_on_unknown";

    /**
     * bool. When true apply strict type checking. JSON types encountered are expected to match the PHP types we're
     * going to deserialize to. If they don't, throw an exception. Default true.
     */
    const STRICT_TYPES = "deserialization::strict_types";

}