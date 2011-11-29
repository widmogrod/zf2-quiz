<?php
return array(
    'display_exceptions' => true,

    'FacebookBundle' => array(
        'setAppIdInHeadScript' => true,
        'appId'                => '322152434467439',
        'secret'               => 'bd125fa90026c5faba6ed397026c53f0',
    ),

    'di' => array(
        'instance' => array(
            'doctrine_connection' => array(
                'parameters' => array(
                    'params' => array(
                        'driver'   => 'pdo_pgsql',
                        'host'     => 'localhost',
                        'port'     => '5432',
                        'user'     => 'quiz_dev',
                        'password' => 'quiz_dev',
                        'dbname'   => 'quiz_dev',
                    ),
                ),
            ),
        ),
    ),
);