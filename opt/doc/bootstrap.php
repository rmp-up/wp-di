<?php

use RmpUp\WpDi\Test\Mirror;

/**
 * Bootstrapping environment
 */

// Tell wp-integration-test where to find WP
if (false === getenv('WP_DIR')) {
    putenv('WP_DIR=' . dirname(__DIR__, 2) . '/srv/');
}

const WP_CLI = true;
require_once __DIR__ . '/../../etc/composer-bin/wp/vendor/autoload.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// wp-cli
const WP_CLI_ROOT = __DIR__ . '/../../etc/composer-bin/wp/vendor/wp-cli/wp-cli';
require_once WP_CLI_ROOT . '/php/utils.php';
require_once WP_CLI_ROOT . '/php/dispatcher.php';
require_once WP_CLI_ROOT . '/php/class-wp-cli.php';
WP_CLI::get_runner()->init_config();

// Actual WP part
const WP_ADMIN = true;
const WP_USE_THEMES = false;
$_SERVER['PHP_SELF'] = __DIR__ . '/../../srv/wp-admin/post.php';
require_once __DIR__ . '/../../etc/composer-bin/phpunit/vendor/pretzlaw/wp-integration-test/lib/bootstrap.php';

// Admin functions (e.g. current_screen)
require_once ABSPATH . 'wp-admin/includes/admin.php';

/**
 * Stubs
 */

const MY_PLUGIN_DIR = __DIR__;
const WPDI_BASE_DIR = __DIR__ . '/../../';
const WPDI_LIB_DIR = WPDI_BASE_DIR . 'lib/';

class MyOwnWidget extends \WP_Widget {
    public function __construct($id = 'rmpup_myOwnWidget', $name = 'rmp-up test', $options = array(), $control = array())
    {
        parent::__construct((string) $id, $name, $options, $control);
    }
}

class_alias(Mirror::class, '\\MyBox');
class_alias(Mirror::class, '\\MyFactory');
class_alias(Mirror::class, '\\MyOwnActionListener');
class_alias(Mirror::class, '\\MyOwnCliCommand');
class_alias(Mirror::class, '\\MyOwnFilterHandler');
class_alias(Mirror::class, '\\MyOwnPostType');

class MyOwnShortcode extends Mirror {
    public function __invoke(...$invoked)
    {
        return json_encode(parent::__invoke(...$invoked));
    }
}

class_alias(Mirror::class, '\\MyWidget');
class_alias(Mirror::class, '\\SomeRepository');
class_alias(Mirror::class, '\\SomeThing');
class_alias(Mirror::class, '\\SomeThingElse');
