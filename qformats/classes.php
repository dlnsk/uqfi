<?php


abstract class question_bank {
    public static function get_all_qtypes ()
    {
        return [];
    }
}

abstract class core_tag_tag {
    public static function is_enabled ($a, $b)
    {
        return false;
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
