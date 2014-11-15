<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;


interface Feature {
    const FAIL_ON_UNKNOWN_PROPERTIES = "deserialization::fail_on_unknown";
    const WARN_ON_UNKNOWN_PROPERTIES = "deserialization::warn_on_unknown";

    const STRICT_TYPES = "deserialization::strict_types";


}