<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overridding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\Cache\StorageFactory,
    Zend\Log\Writer\FirePhp,
    Zend\Log\Writer\FirePhp\FirePhpBridge,
    Zend\Log\Writer\Stream,
    Zend\Log\Logger;
use Loculus\Mvc\View\Http\BadRequestStrategy;

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Cache\Storage\Filesystem' => function($sm){
                $cache = StorageFactory::factory(array(
                    'adapter' => array(
                        'name' => 'filesystem',
                        'options' => array(
                            'cache_dir' => __DIR__ . '/../../data/cache',
                            'ttl' => 604800, // 7 days (a week)
                        )
                    ),
                    'plugins' => array(
                        'exception_handler' => array('throw_exceptions' => false),
                        'serializer'
                    )
                ));

                return $cache;
            },
            'Zend\Log' => function ($sm) {
                $log = new Logger();

                $stream_writer = new Stream(__DIR__ . '/../../data/log/application.log');
                $log->addWriter($stream_writer);

                if (php_sapi_name() != "cli") {
                    // FirePHP caused problems with phpunit
                    $firephp_writer = new FirePhp(new FirePhpBridge(\FirePHP::getInstance(true)));
                    $log->addWriter($firephp_writer);

                    $log->info('FirePHP logging enabled');
                }

                return $log;
            },
            'Loculus\Mvc\View\Http\BadRequestStrategy' => function ($sm) {
                $strategy = new BadRequestStrategy();

                return $strategy;
            },
        )
    ),
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'driverOptions' => array(
                        'charset' => 'utf8',
                        1002 => 'SET NAMES utf8'
                    ),
                    'eventSubscribers' => array(
                        '\Gedmo\Sluggable\SluggableListener'
                    ),
                ),
            )
        ),
        'configuration' => array(
            'annotations' => array(
                'Gedmo\Mapping\Annotation\'' => realpath(__DIR__ . '/../../vendor/gedemo/doctrine-extensions/lib/Gedemo/Mapping/Annotation'),
            )
        ),
    ),
    'locales' => array(
        'default' => 'pl-PL',
        'list' => array(
            'en-US' => 'English',
            'pl-PL' => 'Polski',
        )
    )
);