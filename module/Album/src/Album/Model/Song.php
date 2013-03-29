<?php

namespace Album\Model;

use Doctrine\ORM\Query,
    Doctrine\ORM\Query\Expr\Join,
    Album\Entity\Song as SongEntity,
    Loculus\Model\AbstractModel,
    Loculus\Log;;

class Song extends AbstractModel
{
    public function getSongs($hydrate = Query::HYDRATE_OBJECT)
    {
        $songs = $this->getEntityManager()->getRepository('Album\Entity\Song')->findAll();

        return $songs;
    }

    public function getSongsByAlbum($album_id, $orderBy = '', $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('s')
            ->from('Album\Entity\Song', 's')
            ->innerJoin('s.album', 'a')
            ->where('s.album_id = :id')
            ->orderBy('s.disc, s.position');
        $qb->setParameter('id', $album_id);

        if (isset($orderBy)) {
            switch ($orderBy) {
                case 'name':
                    $orderColumn = 's.name';
                    break;
                case 'duration':
                    $orderColumn = 's.duration';
                    break;
                case 'position':
                case '':
                default:
                    $orderColumn = 's.position';
            }
            $qb->orderBy($orderColumn);
        }

        $query = $qb->getQuery();
        $songs = $query->getResult($hydrate);

        return $songs;
    }

    public function getSong($id, $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('s, a')
            ->from('Album\Entity\Song', 's')
            ->innerJoin('s.album', 'a'/*, Join::WITH, 'a.id = s.album_id'*/)
            ->where('s.id = :id');
        $qb->setParameter('id', $id);
//         $this->getServiceLocator()->get('Zend\Log')->info($qb->getDQL());
        $query = $qb->getQuery();
//         $this->getServiceLocator()->get('Zend\Log')->info($query->getSQL());
        try {
            $song = $query->getSingleResult($hydrate);
        }
        catch(\Exception $e) {
            return false;
        }

        return $song;
    }
}
