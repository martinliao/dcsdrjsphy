<?php

// Debug levels - always keep the values in ascending order!
/** No warnings and errors at all */
define('DEBUG_NONE', 0);
/** Fatal errors only */
define('DEBUG_MINIMAL', E_ERROR | E_PARSE);
/** Errors, warnings and notices */
define('DEBUG_NORMAL', E_ERROR | E_PARSE | E_WARNING | E_NOTICE);
/** All problems except strict PHP warnings */
define('DEBUG_ALL', E_ALL & ~E_STRICT);
/** DEBUG_ALL with all debug messages and strict warnings */
define('DEBUG_DEVELOPER', E_ALL | E_STRICT);

/** Remove any memory limits */
define('MEMORY_UNLIMITED', -1);
/** Standard memory limit for given platform */
define('MEMORY_STANDARD', -2);
/**
 * Large memory limit for given platform - used in cron, upgrade, and other places that need a lot of memory.
 * Can be overridden with $CFG->extramemorylimit setting.
 */
define('MEMORY_EXTRA', -3);
/** Extremely large memory limit - not recommended for standard scripts */
define('MEMORY_HUGE', -4);

/**
 * This function encapsulates the tests for whether an exception was thrown in
 * early init -- either during setup.php or during init of $OUTPUT.
 *
 * If another exception is thrown then, and if we do not take special measures,
 * we would just get a very cryptic message "Exception thrown without a stack
 * frame in Unknown on line 0". That makes debugging very hard, so we do take
 * special measures in default_exception_handler, with the help of this function.
 *
 * @param array $backtrace the stack trace to analyse.
 * @return boolean whether the stack trace is somewhere in output initialisation.
 */
function is_early_init($backtrace) {
    $dangerouscode = array(
        array('function' => 'header', 'type' => '->'),
        array('class' => 'bootstrap_renderer'),
        array('file' => __DIR__.'/setup.php'),
    );
    foreach ($backtrace as $stackframe) {
        foreach ($dangerouscode as $pattern) {
            $matches = true;
            foreach ($pattern as $property => $value) {
                if (!isset($stackframe[$property]) || $stackframe[$property] != $value) {
                    $matches = false;
                }
            }
            if ($matches) {
                return true;
            }
        }
    }
    return false;
}
