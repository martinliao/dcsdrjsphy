<?php

/**
 * Simple javascript output class
 *
 * @copyright 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class Js_writer {

    /**
     * Returns javascript code calling the function
     *
     * @param string $function function name, can be complex like Y.Event.purgeElement
     * @param array $arguments parameters
     * @param int $delay execution delay in seconds
     * @return string JS code fragment
     */
    public static function function_call($function, array $arguments = null, $delay=0) {
        if ($arguments) {
            $arguments = array_map('json_encode', convert_to_array($arguments));
            $arguments = implode(', ', $arguments);
        } else {
            $arguments = '';
        }
        $js = "$function($arguments);";

        if ($delay) {
            $delay = $delay * 1000; // in miliseconds
            $js = "setTimeout(function() { $js }, $delay);";
        }
        return $js . "\n";
    }

    /**
     * Special function which adds Y as first argument of function call.
     *
     * @param string $function The function to call
     * @param array $extraarguments Any arguments to pass to it
     * @return string Some JS code
     */
    public static function function_call_with_Y($function, array $extraarguments = null) {
        if ($extraarguments) {
            $extraarguments = array_map('json_encode', convert_to_array($extraarguments));
            $arguments = 'Y, ' . implode(', ', $extraarguments);
        } else {
            $arguments = 'Y';
        }
        return "$function($arguments);\n";
    }

    /**
     * Returns JavaScript code to initialise a new object
     *
     * @param string $var If it is null then no var is assigned the new object.
     * @param string $class The class to initialise an object for.
     * @param array $arguments An array of args to pass to the init method.
     * @param array $requirements Any modules required for this class.
     * @param int $delay The delay before initialisation. 0 = no delay.
     * @return string Some JS code
     */
    public static function object_init($var, $class, array $arguments = null, array $requirements = null, $delay=0) {
        if (is_array($arguments)) {
            $arguments = array_map('json_encode', convert_to_array($arguments));
            $arguments = implode(', ', $arguments);
        }

        if ($var === null) {
            $js = "new $class(Y, $arguments);";
        } else if (strpos($var, '.')!==false) {
            $js = "$var = new $class(Y, $arguments);";
        } else {
            $js = "var $var = new $class(Y, $arguments);";
        }

        if ($delay) {
            $delay = $delay * 1000; // in miliseconds
            $js = "setTimeout(function() { $js }, $delay);";
        }

        if (count($requirements) > 0) {
            $requirements = implode("', '", $requirements);
            $js = "Y.use('$requirements', function(Y){ $js });";
        }
        return $js."\n";
    }

    /**
     * Returns code setting value to variable
     *
     * @param string $name
     * @param mixed $value json serialised value
     * @param bool $usevar add var definition, ignored for nested properties
     * @return string JS code fragment
     */
    public static function set_variable($name, $value, $usevar = true) {
        $output = '';

        if ($usevar) {
            if (strpos($name, '.')) {
                $output .= '';
            } else {
                $output .= 'var ';
            }
        }

        $output .= "$name = ".json_encode($value).";";

        return $output;
    }

    /**
     * Writes event handler attaching code
     *
     * @param array|string $selector standard YUI selector for elements, may be
     *     array or string, element id is in the form "#idvalue"
     * @param string $event A valid DOM event (click, mousedown, change etc.)
     * @param string $function The name of the function to call
     * @param array $arguments An optional array of argument parameters to pass to the function
     * @return string JS code fragment
     */
    public static function event_handler($selector, $event, $function, array $arguments = null) {
        $selector = json_encode($selector);
        $output = "Y.on('$event', $function, $selector, null";
        if (!empty($arguments)) {
            $output .= ', ' . json_encode($arguments);
        }
        return $output . ");\n";
    }
}