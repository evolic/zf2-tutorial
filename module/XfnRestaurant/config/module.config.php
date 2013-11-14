<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'OrdersService' => 'XfnRestaurant\Factory\Service\OrdersServiceFactory',
        )
    ),
    'controllers' => array(
        'factories' => array(
            'xfn-restaurant/orders' => 'XfnRestaurant\Factory\Controller\OrdersControllerFactory',
        ),
        'invokables' => array(
            'xfn-restaurant/cuisines' => 'XfnRestaurant\Controller\CuisinesController',
            'xfn-restaurant/meals' => 'XfnRestaurant\Controller\MealsController',
            'xfn-restaurant/desserts' => 'XfnRestaurant\Controller\DessertsController',
            'xfn-restaurant/drinks' => 'XfnRestaurant\Controller\DrinksController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'restaurant' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/:locale/restaurant',
                    'defaults' => array(
                        'controller' => 'xfn-restaurant/orders',
                        'action'     => 'index',
                        'page'       => 1,
                        'order_by'   => '',
                    ),
                ),
                'child_routes' => array(
                    'orders' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/orders',
                        ),
                        'child_routes' => array(
                            'general' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/:action[/:id][,[:page],[:order_by]].html',
                                    'constraints' => array(
                                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id'       => '[0-9]+',
                                        'page'     => '[0-9]+',
                                        'order_by' => '[a-z][a-z_]*',
                                    ),
                                ),
                                'may_terminate' => true,
                            ),
                            'make' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/make-an-order.html',
                                    'defaults' => array(
                                        'action' => 'make',
                                    ),
                                ),
                                'may_terminate' => true,
                            ),
                            'order-lunch' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/order-lunch.html',
                                    'defaults' => array(
                                        'action' => 'order-lunch',
                                    ),
                                ),
                                'may_terminate' => true,
                            ),
                            'order-drink' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/order-drink.html',
                                    'defaults' => array(
                                        'action' => 'order-drink',
                                    ),
                                ),
                                'may_terminate' => true,
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'cuisines' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/cuisines',
                            'defaults' => array(
                                'controller' => 'xfn-restaurant/cuisines',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'meals' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/meals',
                            'defaults' => array(
                                'controller' => 'xfn-restaurant/meals',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'desserts' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/desserts',
                            'defaults' => array(
                                'controller' => 'xfn-restaurant/desserts',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'drinks' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/drinks',
                            'defaults' => array(
                                'controller' => 'xfn-restaurant/drinks',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),

    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
                'text_domain' => 'album'
            ),
        ),
    ),

    // View setup for this module
    'view_manager' => array(
        'template_path_stack' => array(
            'xfn-restaurant' => __DIR__ . '/../view',
        ),
    ),
);