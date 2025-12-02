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
    return 'no_string';//__("moodle.$component.$identifier");
}

function shorten_text($text, $limit) {
    return mb_substr($text, 0, $limit);
}

function clean_param($param, $type) {
    return $param;
}
