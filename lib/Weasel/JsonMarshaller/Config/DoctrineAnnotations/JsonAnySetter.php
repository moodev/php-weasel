<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Weasel\Common\Utils\NoUndeclaredProperties;
use Doctrine\Common\Annotations\Annotation;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonAnySetter;

/**
 * @Annotation
 * @Target("METHOD")
 */
class JsonAnySetter extends NoUndeclaredProperties implements IJsonAnySetter
{


}
