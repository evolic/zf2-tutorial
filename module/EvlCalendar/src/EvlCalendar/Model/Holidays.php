<?php

namespace EvlCalendar\Model;

use Doctrine\ORM\Query,
    Doctrine\ORM\Query\Expr\Join;
use EvlCalendar\Entity\Holiday as HolidayEntity;
use Loculus\Model\AbstractModel;

class Holidays extends AbstractModel
{
    const DEFAULT_DATE_FORMAT      = 'Y-m-d';
    const DEFAULT_TIME_FORMAT      = 'H:i:s';
    const DEFAULT_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';


    public function getHolidays($year = null, $orderBy = null, $hydrate = Query::HYDRATE_OBJECT)
    {
        $list = $this->getEntityManager()->getRepository('EvlCalendar\Entity\Holiday')->findAll();
        $this->setupNamespace();
        $key  = 'holidays-' . $year . '-' . $orderBy . '-' . $hydrate;
        $list = $this->getCache()->getItem($key, $success);

        if (!$success) {
            $this->firephp->warn('Reloading holidays..');
            $qb = $this->getEntityManager()->createQueryBuilder()
                ->select('h')
                ->from('EvlCalendar\Entity\Holiday', 'h')
                ;

            if (isset($orderBy)) {
                switch ($orderBy) {
                    case 'name':
                        $orderColumn = 'h.name';
                        break;
                    case 'dated_at':
                        $orderColumn = 'h.dated_at';
                        break;
                    default:
                        $orderColumn = 'h.id';
                }
                $qb->orderBy($orderColumn);
            }

            if (!isset($year)) {
                $year = (int) date('Y');
            }
            $qb->andWhere('h.year = :year');
            $qb->setParameter('year', $year);

            $this->firephp->info($qb->getDQL());
            $query = $qb->getQuery();
            $this->firephp->info($query->getSQL());
            $list = $query->getResult($hydrate);

            $this->getCache()->setItem($key, $list);
            $this->getCache()->setTags($key, array('holidays-' . $year));
        } else {
            $this->firephp->warn('Loaded holidays from cache..');
        }

        return $list;
    }

    public function getHoliday($id, $hydrate = Query::HYDRATE_OBJECT)
    {
//         $item = $this->getEntityManager()->find('EvlCalendar\Entity\Holiday', $id);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('h')
            ->from('EvlCalendar\Entity\Holiday', 'h')
            ->where('h.id = :id');
        $qb->setParameter('id', $id);
        $this->firephp->info($qb->getDQL());
        $query = $qb->getQuery();
        $this->firephp->info($query->getSQL());
        $item = $query->getSingleResult($hydrate);

        return $item;
    }

    public function findByDate($date, $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('h')
            ->from('EvlCalendar\Entity\Holiday', 'h')
            ->where('h.dated_at = :dated_at')
        ;
        $qb->setParameter('dated_at', $date);
        $this->firephp->info($qb->getDQL());
        $query = $qb->getQuery();
        $this->firephp->info($query->getSQL());
        $item = $query->getResult($hydrate);

        return $item;
    }

    public function findByYear($year, $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('h')
            ->from('EvlCalendar\Entity\Holiday', 'h')
            ->where('h.year = :year')
        ;
        $qb->setParameter('year', $year);
        $this->firephp->info($qb->getDQL());
        $query = $qb->getQuery();
        $this->firephp->info($query->getSQL());
        $item = $query->getResult($hydrate);

        return $item;
    }


    public function getHolidaysEvents($hydrate = Query::HYDRATE_OBJECT)
    {
        $oDateTime = new \DateTime();
        $now = $oDateTime->format(self::DEFAULT_TIMESTAMP_FORMAT);
        $oDateTime->modify('-1 year');
        $year_before = $oDateTime->format(self::DEFAULT_TIMESTAMP_FORMAT);
        $oDateTime->modify('+2 year');
        $year_after = $oDateTime->format(self::DEFAULT_TIMESTAMP_FORMAT);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('h')
            ->from('EvlCalendar\Entity\Holiday', 'h')
            ->where('h.dated_at >= :year_before')
            ->andWhere('h.dated_at <= :year_after')
            ->orderBy('h.dated_at')
            ;
        $qb->setParameter('year_before', $year_before);
        $qb->setParameter('year_after', $year_after);
        $this->firephp->info($qb->getDQL());
        $query = $qb->getQuery();
        $this->firephp->info($query->getSQL());
        $records = $query->getResult($hydrate);

        $list = array();
        foreach ($records as $record) {
            $date = $record->dated_at->format(self::DEFAULT_DATE_FORMAT);
            $item = array(
                'id' => $record->id,
                'title' => $record->name,
                'start' => $date,
                'end' => $date,
                'allDay' => true,
            );

            $list[] = $item;
        }

        return $list;
    }

    /**
     * Adds holidays for given year and returns number of created records
     * @param int $year
     * @throws Exception
     * @return number
     */
    public function createHolidaysForYear($year)
    {
        if (!isset($year)) {
            $year = (int) date('Y');
        }

        // number of created records
        $created = 0;

        foreach (HolidayEntity::$holidays as $holiday) {
            $name = $holiday['name'];

            if ($holiday['constant']) {
                $date = $year . $holiday['date'];
            } else {
                switch ($name) {
                    case HolidayEntity::HOLIDAY_NAME_EASTER_SUNDAY:
                        $date = date(self::DEFAULT_DATE_FORMAT , easter_date($year));
                        break;
                    case HolidayEntity::HOLIDAY_NAME_EASTER_MONDAY:
                        $easter_date = date(self::DEFAULT_DATE_FORMAT, easter_date($year));
                        $oDateTime = new \DateTime($easter_date);
                        $oDateTime->modify('+1 day');
                        $date = $oDateTime->format(self::DEFAULT_DATE_FORMAT);
                        break;
                    case HolidayEntity::HOLIDAY_NAME_PENTECOST:
                        $easter_date = date(self::DEFAULT_DATE_FORMAT, easter_date($year));
                        $oDateTime = new \DateTime($easter_date);
                        $oDateTime->modify('+7 week');
                        $date = $oDateTime->format(self::DEFAULT_DATE_FORMAT);
                        break;
                    case HolidayEntity::HOLIDAY_NAME_CORPUS_CHRISTI:
                        $easter_date = date(self::DEFAULT_DATE_FORMAT, easter_date($year));
                        $oDateTime = new \DateTime($easter_date);
                        $oDateTime->modify('+60 day');
                        $date = $oDateTime->format(self::DEFAULT_DATE_FORMAT);
                        break;
                    default:
                        $message = "Invalid holiday name `$name`";
                        throw new Exception($message);
                }
            }

            $oHoliday = $this->findByDate($date);

            if ($oHoliday === false || empty($oHoliday)) {
                // record not found, just add it

                $oHoliday = new HolidayEntity();
                $oHoliday->populate(array(
                    'name' => $name,
                    'year' => $year,
                    'weekday' => $holiday['weekday'] ?: null ,
                    'dated_at' => $date,
                    'type' => HolidayEntity::TYPE_NATIONAL,
                    'constant' => $holiday['constant'],
                ));

                $this->getEntityManager()->persist($oHoliday);
                $this->getEntityManager()->flush();

                $created++;
            }
        }

        if ($created) {
            $this->clearCache($year);
        }

        return $created;
    }

    /**
     * Gets day code appropiate to model e.g. Sunday = 32
     *
     * @param string $date  Optional date provided as a string e.g. `2010-08-15` or `15.07.2010`
     * @return int  Day code
     */
    public function getDayCode($date = null)
    {
        if (isset($date)) {
            $time = strtotime($date);
            $day = date("N", $time);
        } else {
            $day = date("N");
        }

        $day = (int) $day;
        $code = pow(2, $day-1);

        return $code;
    }

    /**
     * Check if date is a holiday against holidays database
     * @param string $date
     * @param bool $acceptSunday
     * @return boolean
     */
    public function isHoliday($date = null, $acceptSunday = false)
    {
        $day = $this->getDayCode($date);

        if ($acceptSunday && $day == HolidayEntity::DAY_SUNDAY) {
            return true;
        } else {
            if (isset($date)) {
                $time = strtotime($date);
                $validDate = date(self::DEFAULT_DATE_FORMAT, $time);
            } else {
                $validDate = date(self::DEFAULT_DATE_FORMAT);
            }

            $oHoliday = $this->findByDate($validDate);

            return $oHoliday !== false;
        }
    }

    public function clearCache($year = null, $id = null)
    {
        $this->setupNamespace();

        // clear list
        if (isset($year) && $year) {
            $this->getCache()->clearByTags(array('holidays-' . $year));
        }

        // clear item
        if (isset($id) && $id) {
            $this->getCache()->clearByTags(array('holiday-' . $id));
        }
    }

    protected function setupNamespace()
    {
        $this->getCache()->getOptions()->setNamespace('holidays');
    }
}