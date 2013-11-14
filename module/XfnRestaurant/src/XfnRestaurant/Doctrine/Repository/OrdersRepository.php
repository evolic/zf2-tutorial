<?php
namespace XfnRestaurant\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use XfnRestaurant\Entity\Drink2Order;
use XfnRestaurant\Entity\Order;

/**
 * Class OrderRepository - orders repository. Provides additional database query methods.
 * @package XfnRestaurant\Doctrine\Repository
 */
class OrdersRepository extends EntityRepository
{
    /**
     * Method used to obtain most recent orders from the database
     *
     * @param array $criteria - additional criteria
     * @return array - assigned teacher subjects
     */
    public function getOrders($criteria, $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o')
            ->from('XfnRestaurant\Entity\Order', 'o');

        if (isset($criteria['order_by']) && $criteria['order_by']) {
            $qb->orderBy('o.' . $criteria['order_by']);
        } else {
            $qb->orderBy('o.created_at', 'DESC');
        }
        if (isset($criteria['limit']) && $criteria['limit']) {
            $qb->setMaxResults($criteria['limit']);
        }

        return $qb->getQuery()->getResult($hydrate);
    }
}