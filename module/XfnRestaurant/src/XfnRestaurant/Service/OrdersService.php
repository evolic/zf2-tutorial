<?php
namespace XfnRestaurant\Service;

use XfnRestaurant\Doctrine\Repository\CuisinesRepository;
use XfnRestaurant\Doctrine\Repository\DessertsRepository;
use XfnRestaurant\Doctrine\Repository\DrinksRepository;
use XfnRestaurant\Doctrine\Repository\LunchesRepository;
use XfnRestaurant\Doctrine\Repository\MealsRepository;
use XfnRestaurant\Doctrine\Repository\OrdersRepository;
use XfnRestaurant\Entity\Drink2Order;
use XfnRestaurant\Entity\Lunch;
use XfnRestaurant\Entity\Order;
use Doctrine\ORM\EntityManager;

/**
 * Class OrdersService - service used to perform basic logic operations on restaurant orders.
 * @package XfnRestaurant\Service
 */
class OrdersService
{
    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     *
     * @param array $params
     * @return boolean
     */
    public function createNewDrinkOrder($params)
    {
        $params['ice-cubes'] = !($params['ice-cubes'] === 'no');
        $params['lemon']     = !($params['lemon'] === 'no');

        $drink = $this->getDrinksRepository()->find($params['drink']);

        if (!$drink) {
            return false;
        }

        $this->getEntityManager()->beginTransaction();
        try {
            // create new order
            $order = new Order();
            $order->price = $drink->price;
            $this->getEntityManager()->persist($order);
            $this->getEntityManager()->flush();

            // link order with a drink
            $drink2order = new Drink2Order();
            $drink2order->drink = $drink;
            $drink2order->order = $order;
            $drink2order->drink_id = $drink->id;
            $drink2order->order_id = $order->id;
            $drink2order->lemon     = $params['lemon'];
            $drink2order->ice_cubes = $params['ice-cubes'];
            $this->getEntityManager()->persist($drink2order);

            // commit transaction
            $this->getEntityManager()->flush();
            $this->getEntityManager()->commit();

            return true;
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
            $this->getEntityManager()->close();
        }

        return false;
    }

    /**
     *
     * @param array $params
     * @return boolean
     */
    public function createNewLunchOrder($params)
    {
        $main_course = $this->getMealsRepository()->find($params['main-course']);
        $dessert     = $this->getDessertsRepository()->find($params['dessert']);

        if (!$main_course || !$dessert) {
            return false;
        }

        $this->getEntityManager()->beginTransaction();
        try {
            // create new order
            $order = new Order();
            $order->price = $main_course->price + $dessert->price;
            $this->getEntityManager()->persist($order);
            $this->getEntityManager()->flush();

            // link order with a drink
            $lunch = new Lunch();
            $lunch->meal    = $main_course;
            $lunch->dessert = $dessert;
            $this->getEntityManager()->persist($lunch);
            $this->getEntityManager()->flush();

            $order->getLunches()->add($lunch);
            $lunch->getOrders()->add($order);
            $this->getEntityManager()->flush();

            // commit transaction
            $this->getEntityManager()->flush();
            $this->getEntityManager()->commit();

            return true;
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
            $this->getEntityManager()->close();
        }

        return false;
    }


    /**
     * Method used to obtain cuisine repository.
     * @return CuisinesRepository
     */
    public function getCuisinesRepository()
    {
        return $this->getEntityManager()->getRepository('XfnRestaurant\Entity\Cuisine');
    }

    /**
     * Method used to obtain dessert repository.
     * @return DessertsRepository
     */
    public function getDessertsRepository()
    {
        return $this->getEntityManager()->getRepository('XfnRestaurant\Entity\Dessert');
    }

    /**
     * Method used to obtain drink repository.
     * @return DrinksRepository
     */
    public function getDrinksRepository()
    {
        return $this->getEntityManager()->getRepository('XfnRestaurant\Entity\Drink');
    }

    /**
     * Method used to obtain lunch repository.
     * @return LunchesRepository
     */
    public function getLunchesRepository()
    {
        return $this->getEntityManager()->getRepository('XfnRestaurant\Entity\Lunch');
    }

    /**
     * Method used to obtain meal repository.
     * @return MealsRepository
     */
    public function getMealsRepository()
    {
        return $this->getEntityManager()->getRepository('XfnRestaurant\Entity\Meal');
    }

    /**
     * Method used to obtain orders repository.
     * @return OrdersRepository
     */
    public function getOrdersRepository()
    {
        return $this->getEntityManager()->getRepository('XfnRestaurant\Entity\Order');
    }


    /**
     * Method used to obtain EntityManager.
     * @return EntityManager - entity manager object
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Method used to inject EntityManager.
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}