<?php

use RmpUp\WpDi\Test\AbstractTestCase;

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

function register_post_type()
{
    _rmp_record_call(__FUNCTION__, func_get_args());
}