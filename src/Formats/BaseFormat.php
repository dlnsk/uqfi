<?php

namespace Dlnsk\UQFI\Formats;

abstract class BaseFormat
{
    protected $qFormatObj = null;
    protected $fileContent = '';

    public function __construct(string $filePath)
    {
        $this->fileContent = file_get_contents($filePath);
    }

    /**
     * Get list of question.
     * Every question has a native Moodle structure.
     *
     * @return array
     */
    public function readQuestions(): array {
        return $this->qFormatObj->readquestions(explode("\n", $this->fileContent));
    }

    /**
     * Transform native Moodle question's structure to structure looking like xml export.
     *
     * @return array
     */
    public function readDecoratedQuestions(): array {
        return $this->readQuestions();
    }
}
