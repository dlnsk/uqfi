<?php

namespace Dlnsk\UQFI\Formats;

class Xml extends BaseFormat
{
    public function __construct($filePath)
    {
        parent::__construct($filePath);
        $this->qFormatObj = new \qformat_xml();
    }
}
