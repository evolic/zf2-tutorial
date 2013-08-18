<?php

namespace EvlCalendar\Model;

use Doctrine\ORM\Query,
    Doctrine\ORM\Query\Expr\Join;
use EvlCalendar\Entity\Event as EventEntity;
use Loculus\Model\AbstractModel;

class Events extends AbstractModel
{
    const DEFAULT_DATE_FORMAT      = 'Y-m-d';
    const DEFAULT_TIME_FORMAT      = 'H:i:s';
    const DEFAULT_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

    /**
     *
     * @param string $starting_at
     * @param string $ending_at
     * @param int $hydrate
     * @return array
     */
    public function getEvents($starting_at, $ending_at, $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from('EvlCalendar\Entity\Event', 'e')
            ;

        $qb->andWhere('e.started_at >= :starting_at');
        $qb->setParameter('starting_at', $starting_at);

        $qb->andWhere('e.ended_at < :ending_at');
        $qb->setParameter('ending_at', $ending_at);

        $query = $qb->getQuery();

        $records = $query->getResult($hydrate);
        $list = array();

        foreach ($records as $record) {
            $started_at = $record->started_at->format(self::DEFAULT_TIMESTAMP_FORMAT);
            $ended_at   = $record->ended_at->format(self::DEFAULT_TIMESTAMP_FORMAT);

            $item = array(
                'id' => $record->id,
                'title' => $record->name,
                'start' => $started_at,
                'end' => $ended_at,
                'allDay' => (bool) $record->all_day,
            );

            $list[] = $item;
        }

        return $list;
    }

    /**
     * Gets event by it's id
     * @param int $id
     * @param int $hydrate
     * @return boolean|array|EventEntity
     */
    public function getEvent($id, $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from('EvlCalendar\Entity\Event', 'e')
            ->where('e.id = :id')
        ;

        $qb->setParameter('id', $id);
        $query = $qb->getQuery();

        try {
            $event = $query->getSingleResult($hydrate);
        }
        catch(\Exception $e) {
            return false;
        }
        return $event;
    }

    public function clearCache($year = null, $id = null)
    {
        $this->setupNamespace();

        // clear list
        if (isset($year) && $year) {
            $this->getCache()->clearByTags(array('events-' . $year));
        }

        // clear item
        if (isset($id) && $id) {
            $this->getCache()->clearByTags(array('event-' . $id));
        }
    }

    protected function setupNamespace()
    {
        $this->getCache()->getOptions()->setNamespace('events');
    }
}