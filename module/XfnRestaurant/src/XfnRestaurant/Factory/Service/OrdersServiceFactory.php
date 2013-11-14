<?php
namespace XfnRestaurant\Factory\Service;

use XfnRestaurant\Service\OrdersService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OrdersServiceFactory - factory used to create OrdersService.
 * @package XfnRestaurant\Factory\Service
 */
class OrdersServiceFactory implements FactoryInterface
{
    /**
     * Factory method.
     * @param ServiceLocatorInterface $serviceLocator
     * @return OrdersService|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new OrdersService();
        $service->setEntityManager($serviceLocator->get('Doctrine\ORM\EntityManager'));
        return $service;
    }
}