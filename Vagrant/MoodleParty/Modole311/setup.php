<?php

/**
 * setup.php - Sets up ... and so on
 * Normally this is only called by the main config.php file
 *
 * @package    core/lib
 * @copyright  2023 Click-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Holds the core settings that affect how MoodleTW works. Some of its fields are set in config.php, and the rest are loaded from the config table.
 *
 * Some typical settings in the $CFG global:
 *  - $CFG->wwwroot  - Path to moodle index directory in url format.
 *  - $CFG->dataroot - Path to moodle data files directory on server's filesystem.
 *  - $CFG->dirroot  - Path to moodle's library folder on server's filesystem.
 *  - $CFG->libdir   - Path to moodle's library folder on server's filesystem.
 *  - $CFG->backuptempdir  - Path to moodle's backup temp file directory on server's filesystem.
 *  - $CFG->tempdir  - Path to moodle's temp file directory on server's filesystem.
 *  - $CFG->cachedir - Path to moodle's cache directory on server's filesystem (shared by cluster nodes).
 *  - $CFG->localcachedir - Path to moodle's local cache directory (not shared by cluster nodes).
 *  - $CFG->localrequestdir - Path to moodle's local temp request directory (not shared by cluster nodes).
 *
 * @global object $CFG
 * @name $CFG
 */
 global $CFG; // this should be done much earlier in config.php before creating new $CFG instance

// We can detect real dirroot path reliably since PHP 4.0.2,
// it can not be anything else, there is no point in having this in config.php
$CFG->dirroot = dirname(__DIR__);

// File permissions on created directories in the $CFG->dataroot
if (!isset($CFG->directorypermissions)) {
    $CFG->directorypermissions = 02777;      // Must be octal (that's why it's here)
}
if (!isset($CFG->filepermissions)) {
    $CFG->filepermissions = ($CFG->directorypermissions & 0666); // strip execute flags
}
// Better also set default umask because developers often forget to include directory permissions in mkdir() and chmod() after creating new files.
if (!isset($CFG->umaskpermissions)) {
    $CFG->umaskpermissions = (($CFG->directorypermissions & 0777) ^ 0777);
}
umask($CFG->umaskpermissions);

// Normalise dataroot - we do not want any symbolic links, trailing / or any other weirdness there
if (!isset($CFG->dataroot)) {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error ');
    }
    echo('Fatal error: $CFG->dataroot is not specified in config.php! Exiting.'."\n");
    exit(1);
}
$CFG->dataroot = realpath($CFG->dataroot);
if ($CFG->dataroot === false) {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error ');
    }
    echo('Fatal error: $CFG->dataroot is not configured properly, directory does not exist or is not accessible! Exiting.'."\n");
    exit(1);
} else if (!is_writable($CFG->dataroot)) {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error ');
    }
    echo('Fatal error: $CFG->dataroot is not writable, admin has to fix directory permissions! Exiting.'."\n");
    exit(1);
}

// Make sure there is some database table prefix.
if (!isset($CFG->prefix)) {
    $CFG->prefix = '';
}
if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
    $CFG->admin = 'admin';   // This is relative to the wwwroot and dirroot
}
$CFG->libdir = $CFG->dirroot .'/lib';
// Allow overriding of tempdir but be backwards compatible
if (!isset($CFG->tempdir)) {
    $CFG->tempdir = "$CFG->dataroot/temp";
}
if (!isset($CFG->backuptempdir)) {
    $CFG->backuptempdir = "$CFG->tempdir/backup";
}
if (!isset($CFG->cachedir)) {
    $CFG->cachedir = "$CFG->dataroot/cache";
}
if (!isset($CFG->localcachedir)) {
    $CFG->localcachedir = "$CFG->dataroot/localcache";
}
if (!isset($CFG->localrequestdir)) {
    $CFG->localrequestdir = sys_get_temp_dir() . '/requestdir';
}
if (!isset($CFG->langotherroot)) {
    $CFG->langotherroot = $CFG->dataroot.'/lang';
}
if (!isset($CFG->langlocalroot)) {
    $CFG->langlocalroot = $CFG->dataroot.'/lang';
}

// sometimes default PHP settings are borked on shared hosting servers, I wonder why they have to do that??
ini_set('precision', 14); // needed for upgrades and gradebook
ini_set('serialize_precision', 17); // Make float serialization consistent on all systems.

// Scripts may request no debug and error messages in output
// please note it must be defined before including the config.php script
// and in some cases you also need to set custom default exception handler
if (!defined('NO_DEBUG_DISPLAY')) {
    if (defined('AJAX_SCRIPT') and AJAX_SCRIPT) {
        // Moodle AJAX scripts are expected to return json data, any PHP notices or errors break it badly,
        // developers simply must learn to watch error log.
        define('NO_DEBUG_DISPLAY', true);
    } else {
        define('NO_DEBUG_DISPLAY', false);
    }
}

if (!defined('NO_OUTPUT_BUFFERING')) {
    define('NO_OUTPUT_BUFFERING', false);
}
define('PHPUNIT_TEST', false);
define('MDL_PERF_TEST', false);
define('CACHE_DISABLE_ALL', false);
define('CACHE_DISABLE_STORES', false);

// Servers should define a default timezone in php.ini, but if they don't then make sure no errors are shown.
date_default_timezone_set(@date_default_timezone_get());
if (!defined('CLI_SCRIPT')) {
    define('CLI_SCRIPT', false);
}

if (defined('WEB_CRON_EMULATED_CLI')) {
    if (!isset($_SERVER['REMOTE_ADDR'])) {
        echo('Web cron can not be executed as CLI script any more, please use admin/cli/cron.php instead'."\n");
        exit(1);
    }
} else if (isset($_SERVER['REMOTE_ADDR'])) {
    if (CLI_SCRIPT) {
        echo('Command line scripts can not be executed from the web interface');
        exit(1);
    }
} else {
    if (!CLI_SCRIPT) {
        echo('Command line scripts must define CLI_SCRIPT before requiring config.php'."\n");
        exit(1);
    }
}

// All web service requests have WS_SERVER == true.
if (!defined('WS_SERVER')) {
    define('WS_SERVER', false);
}

if (!defined('CLI_MAINTENANCE')) {
    define('CLI_MAINTENANCE', false);
}

// Detect ajax scripts - they are similar to CLI because we can not redirect, output html, etc.
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', false);
}

$CFG->yui2version = '2.9.0';
$CFG->yui3version = '3.17.2';
$CFG->yuipatchlevel = 0;
$CFG->yuipatchedmodules = array(
);

// Store settings from config.php in array in $CFG - we can use it later to detect problems and overrides.
if (!isset($CFG->config_php_settings)) {
    $CFG->config_php_settings = (array)$CFG;
    // Forced plugin settings override values from config_plugins table.
    unset($CFG->config_php_settings['forced_plugin_settings']);
    if (!isset($CFG->forced_plugin_settings)) {
        $CFG->forced_plugin_settings = array();
    }
}

if (isset($CFG->debug)) {
    $CFG->debug = (int)$CFG->debug;
} else {
    $CFG->debug = 0;
}
$CFG->debugdeveloper = (($CFG->debug & (E_ALL | E_STRICT)) === (E_ALL | E_STRICT)); // DEBUG_DEVELOPER is not available yet.

if (!defined('MOODLE_INTERNAL')) { // Necessary because cli installer has to define it earlier.
    // Used by library scripts to check they are being called by Moodle.
    define('MOODLE_INTERNAL', true);
}

// core_component can be used in any scripts, it does not need anything else.
require_once($CFG->libdir .'/classes/component.php'); // ToDo

// ToDo
// special support for highly optimised scripts that do not need libraries and DB connection
if (defined('ABORT_AFTER_CONFIG')) {
    if (!defined('ABORT_AFTER_CONFIG_CANCEL')) {
        // hide debugging if not enabled in config.php - we do not want to disclose sensitive info
        error_reporting($CFG->debug);
        if (NO_DEBUG_DISPLAY) {
            // Some parts of Moodle cannot display errors and debug at all.
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        } else if (empty($CFG->debugdisplay)) {
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        } else {
            ini_set('display_errors', '1');
        }
        require_once("$CFG->dirroot/lib/configonlylib.php");
        return;
    }
}

global $DB;
global $SESSION;
global $USER;
global $SITE;

/**
 * A central store of information about the current page we are
 * generating in response to the user's request.
 *
 * @global moodle_page $PAGE
 * @name $PAGE
 */
global $PAGE;

/**
 * $OUTPUT is an instance of core_renderer or one of its subclasses. Use
 * it to generate HTML for output.
 *
 * $OUTPUT is initialised the first time it is used. See {@link bootstrap_renderer}
 * for the magic that does that. After $OUTPUT has been initialised, any attempt
 * to change something that affects the current theme ($PAGE->course, logged in use,
 * httpsrequried ... will result in an exception.)
 *
 * Please note the $OUTPUT is replacing the old global $THEME object.
 *
 * @global object $OUTPUT
 * @name $OUTPUT
 */
global $OUTPUT;

/**
 * Script path including query string and slash arguments without host.
 * @global string $ME
 * @name $ME
 */
global $ME;

/**
 * $FULLME without slasharguments and query string.
 * @global string $FULLSCRIPT
 * @name $FULLSCRIPT
 */
global $FULLSCRIPT;

/**
 * Relative moodle script path '/course/view.php'
 * @global string $SCRIPT
 * @name $SCRIPT
 */
global $SCRIPT;

// The httpswwwroot has been deprecated, we keep it as an alias for backwards compatibility with plugins only.
$CFG->httpswwwroot = $CFG->wwwroot;

require_once($CFG->libdir .'/setuplib.php');        // Functions that MUST be loaded first

if (NO_OUTPUT_BUFFERING) {
    // we have to call this always before starting session because it discards headers!
    disable_output_buffering();
}

// Increase memory limits if possible
raise_memory_limit(MEMORY_STANDARD);


// Put $OUTPUT in place, so errors can be displayed.
$OUTPUT = new bootstrap_renderer();

// ToDo: exeception and error handler? 
// set handler for uncaught exceptions - equivalent to print_error() call
    set_exception_handler('default_exception_handler');
    set_error_handler('default_error_handler', E_ALL | E_STRICT);

// If there are any errors in the standard libraries we want to know!
error_reporting(E_ALL | E_STRICT);

//point pear include path to moodles lib/pear so that includes and requires will search there for files before anywhere else
//the problem is that we need specific version of quickforms and hacked excel files :-(
ini_set('include_path', $CFG->libdir.'/pear' . PATH_SEPARATOR . ini_get('include_path'));

// Register our classloader, in theory somebody might want to replace it to load other hacked core classes.
if (defined('COMPONENT_CLASSLOADER')) {
    spl_autoload_register(COMPONENT_CLASSLOADER);
} else {
    spl_autoload_register('core_component::classloader');
}

// Remember the default PHP timezone, we will need it later.
core_date::store_default_php_timezone();

// Load up standard libraries
require_once($CFG->libdir .'/filterlib.php');       // Functions for filtering test as it is output
require_once($CFG->libdir .'/ajax/ajaxlib.php');    // Functions for managing our use of JavaScript and YUI
require_once($CFG->libdir .'/weblib.php');          // Functions relating to HTTP and content
require_once($CFG->libdir .'/outputlib.php');       // Functions for generating output
require_once($CFG->libdir .'/navigationlib.php');   // Class for generating Navigation structure
require_once($CFG->libdir .'/dmllib.php');          // Database access
require_once($CFG->libdir .'/datalib.php');         // Legacy lib with a big-mix of functions.
require_once($CFG->libdir .'/accesslib.php');       // Access control functions
require_once($CFG->libdir .'/deprecatedlib.php');   // Deprecated functions included for backward compatibility
require_once($CFG->libdir .'/moodlelib.php');       // Other general-purpose functions
require_once($CFG->libdir .'/enrollib.php');        // Enrolment related functions
require_once($CFG->libdir .'/pagelib.php');         // Library that defines the moodle_page class, used for $PAGE
require_once($CFG->libdir .'/blocklib.php');        // Library for controlling blocks
require_once($CFG->libdir .'/grouplib.php');        // Groups functions
require_once($CFG->libdir .'/sessionlib.php');      // All session and cookie related stuff
require_once($CFG->libdir .'/editorlib.php');       // All text editor related functions and classes
require_once($CFG->libdir .'/messagelib.php');      // Messagelib functions
require_once($CFG->libdir .'/modinfolib.php');      // Cached information on course-module instances
require_once($CFG->dirroot.'/cache/lib.php');       // Cache API

// make sure PHP is not severly misconfigured
setup_validate_php_configuration();

// Connect to the database
//setup_DB();

    initialise_cfg();

if (isset($CFG->debug)) {
    $CFG->debug = (int)$CFG->debug;
    error_reporting($CFG->debug);
}  else {
    $CFG->debug = 0;
}
$CFG->debugdeveloper = (($CFG->debug & DEBUG_DEVELOPER) === DEBUG_DEVELOPER);

// Find out if PHP configured to display warnings,
// this is a security problem because some moodle scripts may
// disclose sensitive information.
if (ini_get_bool('display_errors')) {
    define('WARN_DISPLAY_ERRORS_ENABLED', true);
}
// If we want to display Moodle errors, then try and set PHP errors to match.
if (!isset($CFG->debugdisplay)) {
    // Keep it "as is" during installation.
} else if (NO_DEBUG_DISPLAY) {
    // Some parts of Moodle cannot display errors and debug at all.
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else if (empty($CFG->debugdisplay)) {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else {
    // This is very problematic in XHTML strict mode!
    ini_set('display_errors', '1');
}

// Register our shutdown manager, do NOT use register_shutdown_function().
core_shutdown_manager::initialize();

// Verify upgrade is not running unless we are in a script that needs to execute in any case
if (!defined('NO_UPGRADE_CHECK') and isset($CFG->upgraderunning)) {
    if ($CFG->upgraderunning < time()) {
        unset_config('upgraderunning');
    } else {
        print_error('upgraderunning');
    }
}

// enable circular reference collector in PHP 5.3,
// it helps a lot when using large complex OOP structures such as in amos or gradebook
if (function_exists('gc_enable')) {
    gc_enable();
}


// Calculate and set $CFG->ostype to be used everywhere. Possible values are:
// - WINDOWS: for any Windows flavour.
// - UNIX: for the rest
// Also, $CFG->os can continue being used if more specialization is required
$CFG->ostype = 'UNIX';
$CFG->os = PHP_OS;

//debugBreak();
// Configure ampersands in URLs
ini_set('arg_separator.output', '&amp;');

// Work around for a PHP bug   see MDL-11237
ini_set('pcre.backtrack_limit', 20971520);  // 20 MB

// Work around for PHP7 bug #70110. See MDL-52475 .
if (ini_get('pcre.jit')) {
    ini_set('pcre.jit', 0);
}

// Set PHP default timezone to server timezone.
core_date::set_default_server_timezone();

// neutralise nasty chars in PHP_SELF
if (isset($_SERVER['PHP_SELF'])) {
    $phppos = strpos($_SERVER['PHP_SELF'], '.php');
    if ($phppos !== false) {
        $_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0, $phppos+4);
    }
    unset($phppos);
}

// initialise ME's - this must be done BEFORE starting of session!
initialise_fullme();

// define SYSCONTEXTID in config.php if you want to save some queries,
// after install it must match the system context record id.
if (!defined('SYSCONTEXTID')) {
    context_system::instance();
}

// ToDo: Check again.
// init session prevention flag - this is defined on pages that do not want session
if (CLI_SCRIPT) {
    // no sessions in CLI scripts possible
    define('NO_MOODLE_COOKIES', true);

} else if (WS_SERVER) {
    // No sessions possible in web services.
    define('NO_MOODLE_COOKIES', true);

} else if (!defined('NO_MOODLE_COOKIES')) {
    if (empty($CFG->version) or $CFG->version < 2009011900) {
        // no session before sessions table gets created
        define('NO_MOODLE_COOKIES', true);
    } else if (CLI_SCRIPT) {
        // CLI scripts can not have session
        define('NO_MOODLE_COOKIES', true);
    } else {
        define('NO_MOODLE_COOKIES', false);
    }
}

// Set default content type and encoding, developers are still required to use
// echo $OUTPUT->header() everywhere, anything that gets set later should override these headers.
// This is intended to mitigate some security problems.
if (AJAX_SCRIPT) {
    if (!core_useragent::supports_json_contenttype()) {
        // Some bloody old IE.
        @header('Content-type: text/plain; charset=utf-8');
        @header('X-Content-Type-Options: nosniff');
    } else if (!empty($_FILES)) {
        // Some ajax code may have problems with json and file uploads.
        @header('Content-type: text/plain; charset=utf-8');
    } else {
        @header('Content-type: application/json; charset=utf-8');
    }
} else if (!CLI_SCRIPT) {
    @header('Content-type: text/html; charset=utf-8');
}

// Initialise some variables that are supposed to be set in config.php only.
if (!isset($CFG->filelifetime)) {
    $CFG->filelifetime = 60*60*6;
}

// Late profiling, only happening if early one wasn't started
if (!empty($CFG->profilingenabled)) {
    require_once($CFG->libdir . '/xhprof/xhprof_moodle.php');
    profiling_start();
}

// Hack to get around max_input_vars restrictions,
// we need to do this after session init to have some basic DDoS protection.
workaround_max_input_vars();

// Create the $PAGE global - this marks the PAGE and OUTPUT fully initialised, this MUST be done at the end of setup!
if (!empty($CFG->moodlepageclass)) {
    if (!empty($CFG->moodlepageclassfile)) {
        require_once($CFG->moodlepageclassfile);
    }
    $classname = $CFG->moodlepageclass;
} else {
    $classname = 'moodle_page';
}

$PAGE = new $classname();
unset($classname);

// Apache log integration. In apache conf file one can use ${MOODULEUSER}n in
// LogFormat to get the current logged in username in moodle.
// Alternatvely for other web servers a header X-MOODLEUSER can be set which
// can be using in the logfile and stripped out if needed.
set_access_log_user();

// Use a custom script replacement if one exists

// Switch to CLI maintenance mode if required, we need to do it here after all the settings are initialised.
if (isset($CFG->maintenance_later) and $CFG->maintenance_later <= time()) {
    if (!file_exists("$CFG->dataroot/climaintenance.html")) {
        require_once("$CFG->libdir/adminlib.php");
        enable_cli_maintenance_mode();
    }
    unset_config('maintenance_later');
    if (AJAX_SCRIPT) {
        die;
    } else if (!CLI_SCRIPT) {
        redirect(new moodle_url('/'));
    }
}
