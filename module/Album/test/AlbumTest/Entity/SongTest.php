<?php

namespace AlbumTest\Entity;

use AlbumTest\Bootstrap;
use Album\Entity\Album;
use Album\Entity\Song;
use Loculus\Test\PHPUnit\Entity\EntityTestCase;

class SongTest extends EntityTestCase
{
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
            'position' => 2,
            'name' => 'Crusaders',
            'duration' => '00:01:41',
            'disc' => 1
        );

        $song = new Song;
        $song->populate($data);

        // save data
        $this->em->persist($song);
        $this->em->flush();
        $this->em->clear();

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
        $this->assertEquals('Crusaders', $song->name);
        $this->assertEquals($album_id, $song->album_id);
//         $this->assertEquals(3, count($song->album->songs)); // cannot see new record here

        $album = $this->em->getRepository('Album\Entity\Album')->find($album_id);

//         $this->assertEquals(3, $album->songs->count()); // cannot see new record here

//         $this->assertEquals(1, $album->songs[0]->position); // there is wrong songs order in PostgreSQL
//         $this->assertEquals(2, $album->songs[1]->position);
//         $this->assertEquals(3, $album->songs[2]->position);

        $songs = $this->em->getRepository('Album\Entity\Song')->findBy(array(
            'album_id' => $album_id
        ), array('position' => 'asc'));

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
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();
        $result = $dbh->exec("UPDATE sqlite_sequence SET seq = 7 WHERE name='song';");

        parent::tearDown();
    }
}