/**
 * This module depends on the real jquery - and returns the non-global version of it.
 *
 * @module     jquery-private
 * @package    core
 * @copyright  2023 Click-AP {@line https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function ($) {
    // This noConflict call tells JQuery to remove the variable from the global scope - so
    // the only remaining instance will be the sandboxed one.
    return $.noConflict( true );
});
