<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Common\Utils\NoUndeclaredProperties;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonAnyGetter;

/**
 * @Annotation(on="method", max=1)
 */
class JsonAnyGetter extends NoUndeclaredProperties implements IJsonAnyGetter
{


}
