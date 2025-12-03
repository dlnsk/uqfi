<?php

/**
 * This static class provides access to the other question bank.
 *
 * It provides functions for managing question types and question definitions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_bank {
    /** @var array question type name => question_type subclass. */
    private static $questiontypes = array();

    public static function get_all_qtypes ()
    {
        return [];
    }

    public static function is_qtype_installed ($type)
    {
        switch ($type) {
            case 'ddmatch':
                return true;
        }

        return false;
    }

    /**
     * Get the question type class for a particular question type.
     * @param string $qtypename the question type name. For example 'multichoice' or 'shortanswer'.
     * @param bool $mustexist if false, the missing question type is returned when
     *      the requested question type is not installed.
     * @return question_type the corresponding question type class.
     */
    public static function get_qtype($qtypename, $mustexist = true) {
        global $CFG;
        if (isset(self::$questiontypes[$qtypename])) {
            return self::$questiontypes[$qtypename];
        }
        $file = core_component::get_plugin_directory('qtype', $qtypename) . '/questiontype.php';
        if (!is_readable($file)) {
            if ($mustexist || $qtypename == 'missingtype') {
                throw new coding_exception('Unknown question type ' . $qtypename);
            } else {
                return self::get_qtype('missingtype');
            }
        }
        include_once($file);
        $class = 'qtype_' . $qtypename;
        if (!class_exists($class)) {
            throw new coding_exception("Class {$class} must be defined in {$file}.");
        }
        self::$questiontypes[$qtypename] = new $class();
        return self::$questiontypes[$qtypename];
    }
}


abstract class core_tag_tag {
    public static function is_enabled ($a, $b)
    {
        return false;
    }
}


/**
 * Collection of components related methods.
 */
class core_component {
    /**
     * Returns the exact absolute path to plugin directory.
     *
     * @param string $plugintype type of plugin
     * @param string $pluginname name of the plugin
     * @return string full path to plugin directory; null if not found
     */
    public static function get_plugin_directory($plugintype, $pluginname) {
        return __DIR__ . "/../qtypes/$pluginname";
    }
}


/**
 * Base Moodle Exception class
 *
 * Although this class is defined here, you cannot throw a moodle_exception until
 * after moodlelib.php has been included (which will happen very soon).
 *
 * @package    core
 * @subpackage exception
 * @copyright  2008 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_exception extends \Exception {
    /** @var string The name of the string from error.php to print */
    public $errorcode;

    /** @var string The name of module */
    public $module;

    /** @var mixed Extra words and phrases that might be required in the error string */
    public $a;

    /**
     * The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
     *
     * @var string
     */
    public $link;

    /** @var string Optional information to aid the debugging process */
    public $debuginfo;

    /**
     * Constructor
     * @param string $errorcode The name of the string from error.php to print
     * @param string $module name of module
     * @param string $link The url where the user will be prompted to continue.
     * If no url is provided the user will be directed to the site index page.
     * @param mixed $a Extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $module = '', $link = '', $a = null, $debuginfo = null) {
        global $CFG;

        if (empty($module) || $module == 'moodle' || $module == 'core') {
            $module = 'error';
        }

        $this->errorcode = $errorcode;
        $this->module    = $module;
        $this->link      = $link;
        $this->a         = $a;
        $this->debuginfo = is_null($debuginfo) ? null : (string)$debuginfo;

        if (get_string_manager()->string_exists($errorcode, $module)) {
            $message = get_string($errorcode, $module, $a);
            $haserrorstring = true;
        } else {
            $message = $module . '/' . $errorcode;
            $haserrorstring = false;
        }

        $isinphpunittest = (defined('PHPUNIT_TEST') && PHPUNIT_TEST);
        $hasdebugdeveloper = (
            isset($CFG->debugdisplay) &&
            isset($CFG->debug) &&
            $CFG->debugdisplay &&
            $CFG->debug === DEBUG_DEVELOPER
        );

        if ($debuginfo) {
            if ($isinphpunittest || $hasdebugdeveloper) {
                $message = "$message ($debuginfo)";
            }
        }

        if (!$haserrorstring && $isinphpunittest) {
            // Append the contents of $a to $debuginfo so helpful information isn't lost.
            // This emulates what {@link get_exception_info()} does. Unfortunately that
            // function is not used by phpunit.
            $message .= PHP_EOL . '$a contents: ' . print_r($a, true); // phpcs:ignore moodle.PHP.ForbiddenFunctions.Found
        }

        parent::__construct($message, 0);
    }
}
