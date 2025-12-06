<?php

namespace Dlnsk\UQFI;

use Dlnsk\UQFI\Formats\Aiken;
use Dlnsk\UQFI\Formats\BlackBoard6;
use Dlnsk\UQFI\Formats\Gift;
use Dlnsk\UQFI\Formats\MissingWord;
use Dlnsk\UQFI\Formats\Xml;

class Importer
{
    public static function init()
    {
        define("MOODLE_INTERNAL", "dummy");
        require_once(__DIR__ . '/../lib/functions.php');
        require_once(__DIR__ . '/../lib/xmlize.php');
    }

    /**
     * @throws \Exception
     */
    public static function getFormat($format, $filePath) {
        switch ($format) {
            case 'aiken':
                return new Aiken($filePath);
            case 'blackboard6':
                return new BlackBoard6($filePath);
            case 'gift':
                return new Gift($filePath);
            case 'missingword':
                return new MissingWord($filePath);
            case 'xml':
                return new Xml($filePath);
        }

        throw new \Exception("Format '{$format}' not supported");
    }
}
