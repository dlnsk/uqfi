<?php

namespace Dlnsk\UQFI\Formats;

class Aiken extends BaseFormat
{
    public function __construct($filePath)
    {
        parent::__construct($filePath);
        $this->qFormatObj = new \qformat_aiken();
    }
}
