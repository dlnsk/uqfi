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
        $questions = $this->readQuestions();
        foreach ($questions as $question) {
            if ($question->qtype === 'category') {
                continue;
            }
            $this->mergeInto($question, ['fraction', 'feedback', 'tolerance'], 'answer');
            $this->mergeInto($question, 'multiplier', 'unit');
            $this->drownInto($question, 'format', ['questiontext', 'generalfeedback']);
            $this->drownInto($question, 'files', ['generalfeedback']);
            $this->drownFiles($question, ['questiontext', 'answer', 'subquestions']);
            $this->unsetFields($question, [
                'export_process',
                'import_process',
                'length',
            ]);
        }

        return $questions;
    }

    protected function areAllKeysNumeric(array $array): bool {
        $keys = array_keys($array);
        $numericKeys = array_filter($keys, 'is_numeric');

        return count($keys) === count($numericKeys);
    }

    protected function decorateFiles(array $files): array {
        $decorated = [];
        foreach ($files as $file) {
            $decorated[] = array_merge(
                $file['@'],
                ['text' => $file['#']]
            );
        }

        return $decorated;
    }

    protected function drownFiles(&$question, $fields) {
        $fields = is_array($fields) ? $fields : [$fields];
        foreach ($fields as $field) {
            $files = null;
            if (isset($question->$field)) {
                if (isset($question->{$field.'itemid'})) {
                    $question->$field = is_array($question->$field) ? $question->$field : ['text' => $question->$field];
                    $question->$field['files'] = $this->decorateFiles($question->{$field.'itemid'});
                    unset($question->{$field.'itemid'});
                    continue;
                }
                if (is_array($question->$field) && $this->areAllKeysNumeric($question->$field)) {
                    foreach ($question->$field as &$arr) {
                        if (isset($arr['itemid'])) {
                            $arr['files'] = $this->decorateFiles($arr['itemid']);
                            unset($arr['itemid']);
                        }
                    }
                }
            }
        }
    }

    protected function drownInto(&$question, $drowned, $into) {
        $fields = is_array($into) ? $into : [$into];
        foreach ($fields as $field) {
            $drowned_field = $field . $drowned;
            if (isset($question->$field) && isset($question->$drowned_field)) {
                $question->$field = is_array($question->$field) ? $question->$field : ['text' => $question->$field];
                $question->$field[$drowned] = $question->$drowned_field;
                unset($question->$drowned_field);
            }
        }
    }

    protected function mergeInto(&$question, $fields, $into)
    {
        $fields = is_array($fields) ? $fields : [$fields];
        if (isset($question->$into) && is_array($question->$into)) {
            $set = [];
            foreach ($question->$into as $key => $item) {
                $item = is_array($item) ? $item : ['text' => $item];
                foreach ($fields as $field) {
                    if (isset($question->$field)) {
                        $item[$field] = $question->$field[$key];
                    }
                }
                $set[] = $item;
            }
            $question->$into = $set;
            $this->unsetFields($question, $fields);
        }
    }

    protected function unsetFields(&$question, array $fields)
    {
        foreach ($fields as $field) {
            unset($question->$field);
        }
    }
}
