<?php

use RmpUp\WpDi\Test\Mirror;

const MY_PLUGIN_DIR = __DIR__;

class_alias(Mirror::class, '\\SomeThing');
class_alias(Mirror::class, '\\MyOwnCliCommand');
class_alias(Mirror::class, '\\WP_CLI');

// Tell wp-integration-test where to find WP
$_ENV['WP_DIR'] = $_ENV['WP_DIR'] ?: dirname(__DIR__, 2) . '/srv/';

require_once __DIR__ . '/../../vendor/pretzlaw/wp-integration-test/bootstrap.php';
