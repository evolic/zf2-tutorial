<?php

namespace AlbumTest\Entity;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;
use AlbumTest\Bootstrap;
use Album\Entity\Album;
use Album\Entity\Song;
use PHPUnit_Framework_TestCase;

class SongTest extends PHPUnit_Framework_TestCase
{
    /**
     * Service Manager
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $sm;

    /**
     * Doctrine Entity Manager
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Record id
     * @var int
     */
    protected $id;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');

        parent::setUp();
    }


    public function testCountSongsBeforeInsert()
    {
        $album_id = 6;
        $album = $this->em->getRepository('Album\Entity\Album')->find($album_id);

        $this->assertEquals(2, count($album->songs));
    }

    public function testCanInsertNewRecord()
    {
        $album_id = 6;
        $album = $this->em->getRepository('Album\Entity\Album')->find($album_id);
        $data = array(
            'album_id' => $album_id,
            'album' => $album,
            'id' => null,
            'position' => 3,
            'name' => 'Swordplay',
            'duration' => '00:02:01',
            'disc' => 1
        );
        $song = new Song;
        $song->populate($data);
        // save data
        $this->em->persist($song);
        $this->em->flush();

        $this->assertEquals($data['name'], $song->name);
        $this->assertEquals($data['duration'], $song->duration->format('H:i:s'));
        $this->assertInstanceOf('Album\Entity\Album',  $song->album);
        $this->assertEquals($album_id, $song->album->id);
        $this->assertEquals('[Soundtrack] Kingdom of Heaven', $song->album->title);

        return $song->id;
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCountSongsAfterInsert($id)
    {
        $album_id = 6;

        $song = $this->em->getRepository('Album\Entity\Song')->find($id);
        $this->assertInstanceOf('Album\Entity\Song',  $song);
        $this->assertEquals('Swordplay', $song->name);
        $this->assertEquals($album_id, $song->album_id);
        $this->assertEquals(3, count($song->album->songs));

        $album = $this->em->getRepository('Album\Entity\Album')->find($album_id);

        // @FIXME Something wrong is with this assertion
        $this->assertEquals(3, $album->songs->count());

        $this->assertEquals(1, $album->songs[0]->position);
        $this->assertEquals(2, $album->songs[1]->position);
        $this->assertEquals(3, $album->songs[2]->position);

        $songs = $this->em->getRepository('Album\Entity\Song')->findBy(array(
            'album_id' => $album_id
        ));

        $this->assertEquals(3, count($songs));
        $this->assertEquals(1, $songs[0]->position);
        $this->assertEquals(2, $songs[1]->position);
        $this->assertEquals(3, $songs[2]->position);
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanUpdateInsertedRecord($id)
    {
        $song = $this->em->getRepository('Album\Entity\Song')->find($id);
        $data = array(
            'id' => $song->id,
            'album_id' => $song->album_id,
            'album' => $song->album,
            'position' => 4,
            'name' => 'A New World',
            'duration' => '00:04:21',
            'disc' => $song->disc
        );
        $song->populate($data);
        $this->em->flush();

        $this->assertEquals($data['position'], $song->position);
        $this->assertEquals($data['name'], $song->name);
        $this->assertEquals($data['duration'], $song->duration->format('H:i:s'));
    }

    /**
     * @depends testCanInsertNewRecord
     */
    public function testCanRemoveInsertedRecord($id)
    {
        $song = $this->em->getRepository('Album\Entity\Song')->find($id);
        $this->assertInstanceOf('Album\Entity\Song', $song);

        $this->em->remove($song);
        $this->em->flush();

        $song = $this->em->getRepository('Album\Entity\Song')->find($id);
        $this->assertEquals(false, $song);

        $dbh = $this->em->getConnection();
        $result = $dbh->exec("UPDATE sqlite_sequence SET seq = seq - 1 WHERE name='song';");
    }


    public function tearDown()
    {
        unset($this->sm);
        unset($this->em);

        parent::tearDown();
    }
}