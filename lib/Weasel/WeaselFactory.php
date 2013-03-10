<?php
namespace Weasel;

use Weasel\Annotation\AnnotationConfigurator;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\XmlMarshaller\XmlMapper;

interface WeaselFactory
{

    /**
     * @return XmlMapper
     */
    public function getXmlMapperInstance();

    /**
     * @return JsonMapper
     */
    public function getJsonMapperInstance();

}
