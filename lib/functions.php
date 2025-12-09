<?php
// Constants.

// Define text formatting types ... eventually we can add Wiki, BBcode etc.

/**
 * Does all sorts of transformations and filtering.
 */
define('FORMAT_MOODLE',   '0');

/**
 * Plain HTML (with some tags stripped).
 */
define('FORMAT_HTML',     '1');

/**
 * Plain text (even tags are printed in full).
 */
define('FORMAT_PLAIN',    '2');

/**
 * Wiki-formatted text.
 * Deprecated: left here just to note that '3' is not used (at the moment)
 * and to catch any latent wiki-like text (which generates an error)
 * @deprecated since 2005!
 */
define('FORMAT_WIKI',     '3');

/**
 * Markdown-formatted text http://daringfireball.net/projects/markdown/
 */
define('FORMAT_MARKDOWN', '4');



// Parameter constants - every call to optional_param(), required_param()
// or clean_param() should have a specified type of parameter.

/**
 * PARAM_TEXT - general plain text compatible with multilang filter, no other html tags. Please note '<', or '>' are allowed here.
 */
define('PARAM_TEXT', 'text');



function get_config($module, $option) {
    $options = [
        'quiz.shuffleanswers' => false,
        'qtype_multichoice.answernumbering' => true,
    ];

    if (!isset($options["$module.$option"])) {
        throw new \Exception('Unknown configuration option: ' . $option);
    }

    return $options["$module.$option"];
}

function get_string($identifier, $component = 'common') {
    return "$component.$identifier";
}

function shorten_text($text, $limit) {
    $postfix = strlen($text) > $limit ? '...' : '';
    return mb_substr($text, 0, $limit) . $postfix;
}

function clean_param($param, $type) {
    if (in_array($type, [PARAM_TEXT])) {
        return strip_tags($param);
    }
    return $param;
}

/**
 * A helper to replace PHP 8.3 usage of array_keys with two args.
 *
 * There is an indication that this will become a new method in PHP 8.4, but that has not happened yet.
 * Therefore this non-polyfill has been created with a different naming convention.
 * In the future it can be deprecated if a core PHP method is created.
 *
 * https://wiki.php.net/rfc/deprecate_functions_with_overloaded_signatures#array_keys
 *
 * @param array $array
 * @param mixed $filter The value to filter on
 * @param bool $strict Whether to apply a strit test with the filter
 * @return array
 */
function moodle_array_keys_filter(array $array, $filter, bool $strict = false): array {
    return array_keys(array_filter(
        $array,
        function($value, $key) use ($filter, $strict): bool {
            if ($strict) {
                return $value === $filter;
            }
            return $value == $filter;
        },
        ARRAY_FILTER_USE_BOTH
    ));
}
