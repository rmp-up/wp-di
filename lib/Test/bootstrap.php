<?php

use RmpUp\WpDi\Test\Mirror;

const MY_PLUGIN_DIR = __DIR__;

class_alias(Mirror::class, '\\SomeThing');

require_once __DIR__ . '/../../vendor/pretzlaw/wp-integration-test/bootstrap.php';
