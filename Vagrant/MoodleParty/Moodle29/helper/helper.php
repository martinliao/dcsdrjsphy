<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('get_jsrev')) {
    /**
     * Determine the correct JS Revision to use for this load.
     *
     * @return int the jsrev to use.
     */
    function get_jsrev() {
        global $CFG;

        if (empty($CFG->cachejs)) {
            $jsrev = -1;
        } else if (empty($CFG->jsrev)) {
            $jsrev = 1;
        } else {
            $jsrev = $CFG->jsrev;
        }

        return $jsrev;
    }
}

//require_once("$CFG->dirroot/lib/jslib.php");
//require_once("$CFG->dirroot/lib/configonlylib.php");

function get_amd_footercode() {
    global $CFG;
    $output = '';
    $jsrev = get_jsrev();

    #$jsloader = new moodle_url($CFG->httpswwwroot . '/lib/javascript.php');
    $jsloader = base_url('/lib/javascript.php/' . $jsrev . '/');
    #$jsloader->set_slashargument('/' . $jsrev . '/');

    #$requirejsloader = new moodle_url($CFG->httpswwwroot . '/lib/requirejs.php');
    $requirejsloader = base_url('/lib/requirejs.php/' . $jsrev . '/');
    #$requirejsloader->set_slashargument('/' . $jsrev . '/');

    $requirejsconfig = file_get_contents($CFG->dirroot . '/lib/requirejs/moodle-config.js');

    // No extension required unless slash args is disabled.
    $jsextension = '.js';

    $requirejsconfig = str_replace('[BASEURL]', $requirejsloader, $requirejsconfig);
    $requirejsconfig = str_replace('[JSURL]', $jsloader, $requirejsconfig);
    $requirejsconfig = str_replace('[JSEXT]', $jsextension, $requirejsconfig);
    

}