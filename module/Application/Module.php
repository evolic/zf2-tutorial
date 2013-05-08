<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Http\Response;

class Module
{
    public function onBootstrap($e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach('route', function ($event) {
            $sm = $event->getApplication()->getServiceManager();
            $config = $event->getApplication()->getServiceManager()->get('Configuration');
            $localesConfig = $config['locales'];
            $locales = $localesConfig['list'];
            $locale = $event->getRouteMatch()->getParam('locale');

            // unsupported locale provided
            if (!in_array($locale, array_keys($locales))
                && $event->getApplication()->getRequest()->getUri()->getPath() !== '/') {

                $locale = $localesConfig['default'];
                $url = $event->getRouter()->assemble(array(
                    'locale' => $localesConfig['default']
                ), array('name' => 'home'));
                $response = $event->getApplication()->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(Response::STATUS_CODE_302);
                $response->sendHeaders();
                exit;
            }

            // If there is no lang parameter in the route, switch to default locale
            if (empty($locale)) {
                $locale = $localesConfig['default'];
            }

            $translator = $sm->get('translator');
            $translator->setLocale($locale);
        }, -10);

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        /**
         * Log any Uncaught Errors
         */
        $sharedManager = $e->getApplication()->getEventManager()->getSharedManager();
        $sm = $e->getApplication()->getServiceManager();
        $sharedManager->attach('Zend\Mvc\Application', 'dispatch.error', function($event) use ($sm) {
            if ($event->getParam('exception')){
                $sm->get('Zend\Log')->crit($event->getParam('exception'));
            }

            // get view model for layout
            $view = $event->getViewModel();
            $view->setVariable('locale', $sm->get('translator')->getLocale());
        });
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}
