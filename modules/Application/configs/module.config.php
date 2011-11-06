<?php
return array(
    'bootstrap_class' => 'Application\Bootstrap',
    'layout'          => 'layouts/layout.phtml',
    'display_exceptions' => true,

    'di'              => array(
        'instance' => array(
            'alias' => array(
                'index' => 'Quiz\Controller\IndexController',
                'error' => 'Application\Controller\ErrorController',
                'view'  => 'Zend\View\PhpRenderer',
            ),

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

            'Zend\View\HelperLoader' => array(
                'parameters' => array(
                    'map' => array(
                        'url' => 'Application\View\Helper\Url',
                    ),
                ),
            ),

            'Zend\View\HelperBroker' => array(
                'parameters' => array(
                    'loader' => 'Zend\View\HelperLoader',
                ),
            ),

            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options'  => array(
                        'script_paths' => array(
                            'application' => __DIR__ . '/../views',
                        ),
                    ),
                    'broker' => 'Zend\View\HelperBroker',
                ),
            ),
        ),
    ),

    'routes' => array(
        'default' => array(
            'type'    => 'Zend\Mvc\Router\Http\Regex',
            'options' => array(
                'regex'    => '/(?P<controller>[^/]+)(/(?P<action>[^/]+)?)?',
                'spec'     => '/%controller%/%action%',
                'defaults' => array(
                    'controller' => 'error',
                    'action'     => 'index',
                ),
            ),
        ),
        'home' => array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route'    => '/',
                'defaults' => array(
                    'controller' => 'index',
                    'action'     => 'index',
                ),
            ),
        ),
    ),
);
