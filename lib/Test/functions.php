<?php

use RmpUp\WpDi\Test\AbstractTestCase;

function add_action($name, ...$values)
{
    if (!array_key_exists($name, AbstractTestCase::$actions)) {
        AbstractTestCase::$actions[$name] = [];
    }

    AbstractTestCase::$actions[$name][] = $values;
}