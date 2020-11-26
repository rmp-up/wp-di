<?php

if (
    false === is_dir(__DIR__ . '/../vendor/perftools/php-profiler/src/')
    || false === extension_loaded('xhprof')
) {
    // No profiling for you today.
    return;
}

require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Profilers/ProfilerInterface.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Profilers/AbstractProfiler.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Profilers/XHProf.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Profilers/UProfiler.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Profilers/TidewaysXHProf.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Profilers/Tideways.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/SaverFactory.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Saver/SaverInterface.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Saver/UploadSaver.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/ProfilerFactory.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/ProfilingFlags.php';
require_once __DIR__ . '/../vendor/perftools/php-profiler/src/Profiler.php';

$profiler = new \Xhgui\Profiler\Profiler(
    [
        'profiler.flags' => array(
            \Xhgui\Profiler\ProfilingFlags::CPU,
            \Xhgui\Profiler\ProfilingFlags::MEMORY,
            \Xhgui\Profiler\ProfilingFlags::NO_BUILTINS,
            \Xhgui\Profiler\ProfilingFlags::NO_SPANS,
        ),

        'save.handler' => \Xhgui\Profiler\Profiler::SAVER_UPLOAD,

        // http://127.0.0.1:8142/run/import
        'save.handler.upload' => array(
            'uri' => 'http://10.210.9.1:8142/run/import',
            // The timeout option is in seconds and defaults to 3 if unspecified.
            'timeout' => 3,
            // the token must match 'upload.token' config in XHGui
            'token' => '',
        ),

        'profiler.options' => array(
            'ignored_functions' => array(
                'Composer\Autoload\ClassLoader::loadClass',
                'Composer\Autoload\ClassLoader::findFile',
                'Composer\Autoload\ClassLoader::findFileWithExtension',
                'Composer\Autoload\includeFile',
            ),
        ),
    ]
);

$profiler->start();

require_once __DIR__ . '/../vendor/autoload.php';