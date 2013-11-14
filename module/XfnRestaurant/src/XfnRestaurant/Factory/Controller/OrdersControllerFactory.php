<?php
namespace XfnRestaurant\Factory\Controller;

use XfnRestaurant\Controller\OrdersController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OrdersFactory - factory used to create OrdersController.
 * @package XfnRestaurant\Factory\Controller
 */
class OrdersControllerFactory implements FactoryInterface
{
    /**
     * Factory method.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return OrdersController|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $orderDrinkForm = $serviceLocator->getServiceLocator()->get('FormElementManager')->get('XfnRestaurant\Form\OrderDrinkForm');
        $orderLunchForm = $serviceLocator->getServiceLocator()->get('FormElementManager')->get('XfnRestaurant\Form\OrderLunchForm');

        $ctr = new OrdersController();
        $ctr->setOrderDrinkForm($orderDrinkForm);
        $ctr->setOrderLunchForm($orderLunchForm);
        $ctr->setOrdersService($serviceLocator->getServiceLocator()->get('OrdersService'));
        return $ctr;
    }
}