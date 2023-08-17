<?php

/**
 * Get configuration values from the global config table
 * or the config_plugins table.
 *
 * If called with one parameter, it will load all the config
 * variables for one plugin, and return them as an object.
 *
 * If called with 2 parameters it will return a string single
 * value or false if the value is not found.
 *
 * NOTE: this function is called from lib/db/upgrade.php
 *
 * @static string|false $siteidentifier The site identifier is not cached. We use this static cache so
 *     that we need only fetch it once per request.
 * @param string $plugin full component name
 * @param string $name default null
 * @return mixed hash-like object or single value, return false no config found
 * @throws dml_exception
 */
function get_config($plugin, $name = null) {
    global $CFG, $DB;

    static $siteidentifier = null;

    if ($plugin === 'moodle' || $plugin === 'core' || empty($plugin)) {
        $forced =& $CFG->config_php_settings;
        $iscore = true;
        $plugin = 'core';
    } else {
        if (array_key_exists($plugin, $CFG->forced_plugin_settings)) {
            $forced =& $CFG->forced_plugin_settings[$plugin];
        } else {
            $forced = array();
        }
        $iscore = false;
    }

    if ($siteidentifier === null) {
        try {
            // This may fail during installation.
            // If you have a look at {@link initialise_cfg()} you will see that this is how we detect the need to
            // install the database.
            $siteidentifier = $DB->get_field('config', 'value', array('name' => 'siteidentifier'));
        } catch (dml_exception $ex) {
            // Set siteidentifier to false. We don't want to trip this continually.
            $siteidentifier = false;
            throw $ex;
        }
    }

    if (!empty($name)) {
        if (array_key_exists($name, $forced)) {
            return (string)$forced[$name];
        } else if ($name === 'siteidentifier' && $plugin == 'core') {
            return $siteidentifier;
        }
    }

    $cache = cache::make('core', 'config');
    $result = $cache->get($plugin);
    if ($result === false) {
        // The user is after a recordset.
        if (!$iscore) {
            $result = $DB->get_records_menu('config_plugins', array('plugin' => $plugin), '', 'name,value');
        } else {
            // This part is not really used any more, but anyway...
            $result = $DB->get_records_menu('config', array(), '', 'name,value');;
        }
        $cache->set($plugin, $result);
    }

    if (!empty($name)) {
        if (array_key_exists($name, $result)) {
            return $result[$name];
        }
        return false;
    }

    if ($plugin === 'core') {
        $result['siteidentifier'] = $siteidentifier;
    }

    foreach ($forced as $key => $value) {
        if (is_null($value) or is_array($value) or is_object($value)) {
            // We do not want any extra mess here, just real settings that could be saved in db.
            unset($result[$key]);
        } else {
            // Convert to string as if it went through the DB.
            $result[$key] = (string)$value;
        }
    }

    return (object)$result;
}