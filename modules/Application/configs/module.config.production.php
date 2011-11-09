<?php
return array(
    'display_exceptions' => false,

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
