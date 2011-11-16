<?php
return array(
    'display_exceptions' => false,

    'FacebookBundle' => array(
        'setAppIdInHeadScript' => true,
        'appId'                => '322152434467439',
        'secret'               => 'bd125fa90026c5faba6ed397026c53f0',
    ),

    'di' => array(
        'instance' => array(
            'doctrine' => array(
                'parameters' => array(
                    'conn' => array(
                        'driver'   => 'pdo_pgsql',
                        'host'     => 'localhost',
                        'port'     => '5432',
                        'user'     => 'tomatoe',
                        'password' => 'zombie69',
                        'dbname'   => 'tomatoe',
                    ),
                ),
            ),
        ),
    ),
);
