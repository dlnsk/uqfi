<?php

namespace Dlnsk\UQFI\Formats;

class MissingWord extends BaseFormat
{
    public function __construct($filePath)
    {
        parent::__construct($filePath);
        $this->qFormatObj = new \qformat_missingword();
    }
}
