<?php
return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'quizadmin' => 'Quiz\Controller\AdminController',
                'quizapp' => 'Quiz\Controller\IndexController',
                //'quiz-model' => 'Quiz\Model\Front',
                'quiz-model' => 'Quiz\Model\Front\Mock',
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

            'quiz-model' => array(
                'parameters' => array(
                    'facebook' => 'facebook',
                    'entityManager' => 'doctrine_em'
                )
            ),

            'assetic-configuration' => array(
                'parameters' => array(
                    'config' => array(

                        'cacheEnabled' => true,

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

            'doctrine_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(
                        'application_annotation_driver' => array(
                            'class'           => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                            'namespace' => 'Quiz\Entity',
                            'paths'           => array(__DIR__ . '/../src/Quiz/Entity'),
                        ),
                    )
                )
            ),

            'doctrine' => array(
                'parameters' => array(
                    'config' => array(
                        'auto_generate_proxies' => false,
                        'proxy_dir' => __DIR__ . '/../../../data/SpiffyDoctrine/Proxy',
                        'metadata_driver_impl' => array(
                            'doctrine_annotationdriver' => array(
                                'cache_class' => 'Doctrine\Common\Cache\ApcCache'
                            )
                        ),
                        'metadata_cache_impl' => 'Doctrine\Common\Cache\ApcCache',
                        'query_cache_impl'    => 'Doctrine\Common\Cache\ApcCache',
                        'result_cache_impl'   => 'Doctrine\Common\Cache\ApcCache'
                    ),
                ),
            ),
        ),
    ),
);
