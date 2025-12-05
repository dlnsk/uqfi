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
     * Transform native Moodle question's structure to structure
     * looking like Moodle XML export.
     *
     * @return array
     */
    public function readDecoratedQuestions(): array {
        $questions = $this->readQuestions();
        foreach ($questions as $question) {
            $this->mergeInto($question, ['fraction', 'feedback', 'tolerance'], 'answer');
            $this->mergeInto($question, 'multiplier', 'unit');
            $this->drownInto($question, 'format', ['questiontext', 'generalfeedback']);
            $this->drownInto($question, 'files', ['generalfeedback']);
            $this->drownFiles($question, ['questiontext', 'answer', 'subquestions']);
            $this->mergeInto($question, ['subanswers' => 'answer'], 'subquestions'); // Merge with renaming
            $this->unsetFields($question, [
                'export_process',
                'import_process',
                'length',
            ]);
        }

        return $questions;
    }

    /**
     * Check that array is list (have numeric keys).
     *
     * @param array $array
     * @return bool
     */
    protected function areAllKeysNumeric(array $array): bool {
        $keys = array_keys($array);
        $numericKeys = array_filter($keys, 'is_numeric');

        return count($keys) === count($numericKeys);
    }

    /**
     * Decorate file's data as a single object.
     *
     * @param array $files
     * @return array
     */
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

    /**
     * Transform a question to put file's data into owner object.
     *
     * @param $question
     * @param $fields
     * @return void
     */
    protected function drownFiles(&$question, $fields) {
        $fields = is_array($fields) ? $fields : [$fields];
        foreach ($fields as $field) {
            $files = null;
            if (isset($question->$field)) {
                // Single field with postfix
                if (isset($question->{$field.'itemid'})) {
                    $question->$field = is_array($question->$field) ? $question->$field : ['text' => $question->$field];
                    $question->$field['files'] = $this->decorateFiles($question->{$field.'itemid'});
                    unset($question->{$field.'itemid'});
                    continue;
                }
                // Countable field with numeric keys like 'answer'
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

    /**
     * Transform a question to put connected (by name) data into target field.
     * For example, it puts 'questiontextformat' into 'questiontext'
     *
     * @param $question
     * @param $drowned
     * @param $into
     * @return void
     */
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

    /**
     * Merge number of fields into the one by key.
     *
     * @param $question
     * @param $fields
     * @param $into
     * @return void
     */
    protected function mergeInto(&$question, $fields, $into)
    {
        $fields = is_array($fields) ? $fields : [$fields];
        // Transform ['a' => 'b', 5 => 'c'] into ['a' => 'b', 'c' => 'c']
        // to use it for renaming fields in future
        $fields = array_merge(...array_map(function ($k, $v) {
            $key = is_numeric($k) ? $v : $k;
            return [$key => $v];
        }, array_keys($fields), $fields));

        if (isset($question->$into) && is_array($question->$into)) {
            $set = [];
            foreach ($question->$into as $key => $item) {
                $item = is_array($item) ? $item : ['text' => $item];
                foreach ($fields as $old_name => $new_name) {
                    if (isset($question->$old_name)) {
                        $item[$new_name] = $question->$old_name[$key];
                    }
                }
                $set[] = $item;
            }
            $question->$into = $set;
            $this->unsetFields($question, array_keys($fields));
        }
    }

    /**
     * Unset list of fields.
     *
     * @param $question
     * @param array $fields
     * @return void
     */
    protected function unsetFields(&$question, array $fields)
    {
        foreach ($fields as $field) {
            unset($question->$field);
        }
    }
}
