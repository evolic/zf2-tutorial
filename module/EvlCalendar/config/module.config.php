<?php

namespace EvlCalendar;


return array(

    // Controllers in this module
    'controllers' => array(
        'invokables' => array(
            'evl-calendar/holidays' => 'EvlCalendar\Controller\HolidaysController',
            'evl-calendar/events' => 'EvlCalendar\Controller\EventsController',
        ),
    ),

    'view_helpers' => array(
        'invokables' => array(
            'holidayType' => 'EvlCalendar\View\Helper\HolidayType',
        )
    ),

    // Routes for this module
    'router' => array(
        'routes' => array(
            'holidays' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/holidays[/:action][,[:page],[:order_by]][/[:year]].html',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'     => '[0-9]+',
                        'order_by' => '[a-z][a-z_]*',
                        'year'       => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'evl-calendar/holidays',
                        'action'     => 'index',
                        'page'       => 1,
                        'order_by'   => '',
                    ),
                ),
            ),
            'holidays_json' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/holidays[/:action][/[:year]].json',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'year'       => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'evl-calendar/holidays',
                        'action'     => 'index',
                    ),
                ),
            ),
            'events' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/events[/:action][/[:year]].html',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'year'       => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'evl-calendar/events',
                        'action'     => 'index',
                    ),
                ),
            ),
            'events_json' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/events[/:action][/[:year]].json',
                    'constraints' => array(
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'year'       => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'evl-calendar/events',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
                'text_domain' => 'evl-calendar'
            ),
        ),
    ),

    // View setup for this module
    'view_manager' => array(
        'template_path_stack' => array(
            'evl-calendar' => __DIR__ . '/../view',
        ),
    ),

);
