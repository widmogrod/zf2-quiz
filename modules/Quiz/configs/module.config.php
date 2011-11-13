<?php
return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'quizadmin' => 'Quiz\Controller\AdminController',
                'quizapp' => 'Quiz\Controller\IndexController',
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

            'assetic-configuration' => array(
                'parameters' => array(
                    'config' => array(
                        'controllers' => array(
                            'quizapp' => array(
                                '@quiz_app_css',
                            ),
                            'quizadmin' => array(
                                '@twitter_bootstrap_css',
                                '@quiz_admin_css',
                                '@quiz_admin_js'
                            ),
                        ),

                        'modules' => array(
                            'quiz' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                    'quiz_app_css' => array(
                                        'assets' => array(
                                            'css/reset.css',
                                            'css/app.css',
                                        ),
//                                        'filters' => array(
//                                            'cssembedfilter' => array('name' => 'Assetic\Filter\CssEmbedFilter')
//                                        )
                                    ),
                                    'quiz_app_images' => array(
                                        'assets' => array(
                                            'images/*.png',
                                        ),
                                        'options' => array(
                                            'move_raw' => true,
                                        )
                                    ),

                                    'quiz_admin_css' => array(
                                        'assets' => array(
                                            'css/admin/admin.css'
                                        )
                                    ),

                                    'quiz_admin_js' => array(
                                        'assets' => array(
                                            'http://html5shim.googlecode.com/svn/trunk/html5.js'
                                        )
                                    )
                                ),
                            ),
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
