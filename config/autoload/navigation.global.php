<?php

return array(
    // All navigation-related configuration is collected in the 'navigation' key
    'navigation' => array(
        // The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
        'default' => array(
            // And finally, here is where we define our page hierarchy
            'dashboard' => array(
                'label' => 'Dashboard',
                'route' => 'default',
                'pages' => array(
                    'list_holidays' => array(
                        'Label' => 'List of holidays',
                        'route' => 'holidays',
                        'action' => 'list',
                        'pages' => array()
                    ),

                    'events_calendar' => array(
                        'Label' => 'Calendar of events',
                        'route' => 'events',
                        'action' => 'calendar',
                        'pages' => array()
                    ),

                    'login' => array(
                        'label' => 'Sign In',
                        'route' => 'login',
                    ),
                    'logout' => array(
                        'label' => 'Sign Out',
                        'route' => 'logout',
                    ),
//                     'register' => array(
//                         'label' => 'Register',
//                         'route' => 'user/register',
//                     ),
                ),
            ),
        ),
    ),
);
