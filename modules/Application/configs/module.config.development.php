<?php
return array(
    'display_exceptions' => true,

    'di' => array(
        'instance' => array(
            'doctrine' => array(
                'parameters' => array(
                    'conn' => array(
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