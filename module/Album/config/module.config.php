<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'album/album' => 'Album\Controller\AlbumController',
            'album/song' => 'Album\Controller\SongController'
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'album' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/album[/:action][/:id][,[:page],[:order_by]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'page'     => '[0-9]+',
                        'order_by' => '[a-z][a-z_]*',
                    ),
                    'defaults' => array(
                        'controller' => 'album/album',
                        'action'     => 'index',
                        'page'       => 1,
                        'order_by'   => '',
                    ),
                ),
            ),
            'song' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/song[/:album_id][/:action][/:id][,[:page],[:order_by]]',
                    'constraints' => array(
                        'album_id' => '[0-9]+',
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]+',
                        'page'     => '[0-9]+',
                        'order_by' => '[a-z][a-z_]*',
                    ),
                    'defaults' => array(
                        'controller' => 'album/song',
                        'action'     => 'index',
                        'page'       => 1,
                        'order_by'   => '',
                    ),
                ),
            ),
        ),
    ),

    // View setup for this module
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);