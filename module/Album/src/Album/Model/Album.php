<?php

namespace Album\Model;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Query,
    Doctrine\ORM\Query\Expr\Join,
    Album\Entity\Album as AlbumEntity;

class Album
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $serviceLocator;


    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function setServiceLocator(ServiceManager $sl)
    {
        $this->serviceLocator = $sl;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }


    public function __construct(EntityManager $em)
    {
        $this->setEntityManager($em);
    }


    public function getAlbums($hydrate = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('Album\Entity\Album', 'a')
            ;
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
        $album = $query->getSingleResult($hydrate);
        return $album;
    }
}