<?php
return array(
    'router' => array(
        'routes' => array(
            'choose-lang' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/:locale',
                    'constraints' => array(
                        'locale' => '[a-z]{2}(-[A-Z]{2}){0,1}'
                    ),
                    'defaults' => array(
                        'controller' => 'album/album',
                        'action'     => 'index'
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:locale]/application',
                    'constraints' => array(
                        'locale' => '[a-z]{2}(-[A-Z]{2}){0,1}'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'zf2',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route' => '[/:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'not_supported_locale' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '[/:locale]/not-supported-locale.html',
                    'constraints' => array(
                        'locale' => '[a-z]{2}(-[A-Z]{2}){0,1}'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'not-supported-locale',
                    ),
                ),
            )
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
        'services' => array(
            'session' => new Zend\Session\Container('zf2tutorial'),
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'pl-PL',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'bad_request_template'     => 'error/400',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/400'               => __DIR__ . '/../view/error/400.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'Loculus\Mvc\View\Http\BadRequestStrategy',
        ),
    ),

    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            'Album_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
//                     realpath(__DIR__ . '/../../Album/src/Album/Entity'),
                    str_replace('\\', '/', realpath(__DIR__ . '/../../Album/src/Album/Entity')) // windows
                )
            ),
            'EvlCalendar_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
//                     realpath(__DIR__ . '/../../EvlCalendar/src/EvlCalendar/Entity'),
                    str_replace('\\', '/', realpath(__DIR__ . '/../../EvlCalendar/src/EvlCalendar/Entity')) // windows
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Album\Entity' => 'Album_driver',
                    'EvlCalendar\Entity' => 'EvlCalendar_driver',
                ),
            )
        )
    ),
);
