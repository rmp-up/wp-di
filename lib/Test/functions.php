<?php

use RmpUp\WpDi\Test\AbstractTestCase;

// WordPres Methods
const WP_BASE_PATH = __DIR__ . '/../../srv/';
$wpFunctions = [
    WP_BASE_PATH . 'wp-includes/shortcodes.php',
];

foreach ($wpFunctions as $wpFile) {
    if (file_exists($wpFile)) {
        require_once $wpFile;
    }
}

function _rmp_record_call(string $function, array $arguments)
{
    if (!array_key_exists($function, AbstractTestCase::$calls)) {
        AbstractTestCase::$calls[$function] = [];
    }

    AbstractTestCase::$calls[$function][] = $arguments;
}

function add_action($name, ...$values)
{
    if (!array_key_exists($name, AbstractTestCase::$actions)) {
        AbstractTestCase::$actions[$name] = [];
    }

    AbstractTestCase::$actions[$name][] = $values;
}

function do_action($name, ...$arguments)
{
    if (!array_key_exists($name, AbstractTestCase::$actions)) {
        return;
    }

    foreach (AbstractTestCase::$actions[$name] as $action) {
        $callback = reset($action);
        $callback(...$arguments);
    }
}

function add_filter()
{
    _rmp_record_call(__FUNCTION__, func_get_args());
}

function get_option()
{
    _rmp_record_call(__FUNCTION__, func_get_args());
}

function register_post_type()
{
    _rmp_record_call(__FUNCTION__, func_get_args());
}

function register_widget()
{
    _rmp_record_call(__FUNCTION__, func_get_args());
}

function locate_template()
{
    _rmp_record_call(__FUNCTION__, func_get_args());
}

class WP_CLI
{

}
