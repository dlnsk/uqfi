<?php

namespace Dlnsk\UQFI\Formats;

use PhpZip\ZipFile;

class BlackBoard6 extends BaseFormat
{
    protected $quizzes = [];

    public function __construct($filePath)
    {
        $this->qFormatObj = new \qformat_blackboard_six();
        $zipFile = new ZipFile();
        $zipFile->openFile($filePath);
        $manifest = $zipFile->getEntryContents('imsmanifest.xml');
        $isQTI = strpos($manifest, 'assessment/x-bb-pool') === false;

        $files = array_filter($zipFile->getListFiles(), function ($fileName) {
            return strtolower(substr($fileName, -4)) === '.dat';
        });
        foreach ($files as $fileName) {
            $file = new \qformat_blackboard_six_file();
            $file->text = $zipFile->getEntryContents($fileName);
            $file->filetype = $isQTI ? \qformat_blackboard_six::FILETYPE_QTI : \qformat_blackboard_six::FILETYPE_POOL;
            $this->quizzes[] = $file;
        }
    }

    public function readQuestions(): array {
        return $this->qFormatObj->readquestions($this->quizzes);
    }

}
