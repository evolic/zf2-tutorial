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

use Zend\Log\Writer\FirePhp,
    Zend\Log\Writer\FirePhp\FirePhpBridge,
    Zend\Log\Writer\Stream,
    Zend\Log\Logger;

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Log' => function ($sm) {
                $log = new Logger();

                $firephp_writer = new FirePhp(new FirePhpBridge(\FirePHP::getInstance(true)));
                $log->addWriter($firephp_writer);

                $stream_writer = new Stream('./data/log/application.log');
                $log->addWriter($stream_writer);

                $log->info('FirePHP logging enabled');

                return $log;
            },
        )
    )
);