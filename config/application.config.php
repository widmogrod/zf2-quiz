<?php
return array(
    'modules' => array(
        'FacebookBundle',
        'DataGridBundle',
        'TwitterBootstrap',
        'AsseticBundle',
        'SpiffyDoctrine',
        'Quiz',
        'Application',
    ),

    'module_listener_options' => array(
        'config_cache_enabled'    => false,
        'cache_dir'               => realpath(dirname(__DIR__) . '/data/cache'),
        'module_paths' => array(
            realpath(__DIR__ . '/../modules'),
        ),
//        'application_environment' => getenv('APPLICATION_ENV'),
//        'application_environment' => 'development',
    ),

    'module_manager_options' => array(
        'enable_config_cache'      => false,
        'cache_dir'                => realpath(__DIR__ . '/../data/cache'),
        'enable_dependency_check'  => false,
        'enable_auto_installation' => false,
        'manifest_dir'             => realpath(__DIR__ . '/../data'),
    ),
);
