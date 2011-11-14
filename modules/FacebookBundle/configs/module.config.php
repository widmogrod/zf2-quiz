<?php
return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'quizadmin' => 'Quiz\Controller\AdminController',
                'quizapp' => 'Quiz\Controller\IndexController',
                'facebook' => 'Facebook',
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

            'facebook' => array(
                'parameters' => array(
                    'config' => array(
                        'appId'  => '322152434467439',
                        'secret' => 'bd125fa90026c5faba6ed397026c53f0',
                    )
                )
            ),

            'assetic-configuration' => array(
                'parameters' => array(
                    'config' => array(
                        'controllers' => array(
                            'quizapp' => array(
                                '@quiz_app_css',
                                '@quiz_app_js',
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
                                            'images/*.gif',
                                        ),
                                        'options' => array(
                                            'move_raw' => true,
                                        )
                                    ),
                                    'quiz_app_js' => array(
                                        'assets' => array(
                                            'js/jquery.min.js',
                                            'js/quiz.js',
                                        )
                                    ),

                                    'quiz_admin_css' => array(
                                        'assets' => array(
                                            'css/admin/admin.css'
                                        )
                                    ),

                                    'quiz_admin_js' => array(
                                        'assets' => array(
                                            'js/jquery.min.js',
                                            'http://html5shim.googlecode.com/svn/trunk/html5.js',
                                            'js/admin.js',
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
