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
                    'route'    => '/:locale/album[/:action][/:id][,[:page],[:order_by]]',
                    'constraints' => array(
                        'locale' => '[a-z]{2}(-[A-Z]{2}){0,1}',
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
                    'route'    => '/:locale/song[/:album_id][/:action][/:id][,[:page],[:order_by]]',
                    'constraints' => array(
                        'locale' => '[a-z]{2}(-[A-Z]{2}){0,1}',
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
            'album' => __DIR__ . '/../view',
        ),
    ),
);