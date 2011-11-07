<?php
return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'quizadmin' => 'Quiz\Controller\AdminController',
            ),

            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'options'  => array(
                        'script_paths' => array(
                            'quiz' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),

            'doctrine' => array(
                'parameters' => array(
                    'config' => array(
                        'auto-generate-proxies'     => true,
                        // @todo: figure out how to de-couple the Proxy dir
                        'proxy-dir'                 => __DIR__ . '/../src/Quiz/Proxy',
                        'proxy-namespace'           => 'Quiz\Proxy',
                        'metadata-driver-impl'      => array(
                             'application-annotation-driver' => array(
                                 'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                                 'namespace' => 'Quiz\Entity',
                                 'paths' => array(
                                     __DIR__ . '/../src/Quiz/Entity'
                                 ),
                                 'cache-class' => 'Doctrine\Common\Cache\ArrayCache',
                             )
                        ),
                    ),
                ),
            ),
        ),
    ),
);
