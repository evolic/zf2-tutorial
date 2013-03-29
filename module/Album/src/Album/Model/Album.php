<?php

namespace Album\Model;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Query,
    Doctrine\ORM\Query\Expr\Join,
    Album\Entity\Album as AlbumEntity,
    Loculus\Model\AbstractModel;

class Album extends AbstractModel
{
    public function getAlbums($orderBy = null, $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('Album\Entity\Album', 'a')
            ;
        if (isset($orderBy)) {
            switch ($orderBy) {
                case 'artist':
                    $orderColumn = 'a.artist';
                    break;
                case 'title':
                    $orderColumn = 'a.title';
                    break;
                default:
                    $orderColumn = 'a.id';
            }
            $qb->orderBy($orderColumn);
        }
        $query = $qb->getQuery();
        $list = $query->getResult($hydrate);

        return $list;
    }

    public function getAlbum($id, $hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('Album\Entity\Album', 'a')
            ->where('a.id = :id')
            ;
        $qb->setParameter('id', $id);
        $query = $qb->getQuery();
        try {
            $album = $query->getSingleResult($hydrate);
        }
        catch(\Exception $e) {
            return false;
        }
        return $album;
    }
}