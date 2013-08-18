<?php

namespace EvlCalendar;


use Zend\Mvc\MvcEvent;
use Generations\View\Helper\SemesterType;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'Loculus' => __DIR__ . '../../vendor/Loculus',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfiguration()
    {
        return array(
            'factories' => array(
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                    // the array key here is the name you will call the view helper by in your view scripts
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $sm->get('Zend\Log')->notice(__NAMESPACE__ . '\\' . __CLASS__ . '::onBootstrap');
    }

}
