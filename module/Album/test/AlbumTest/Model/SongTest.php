<?php

namespace AlbumTest\Model;

use Doctrine\ORM\Query;
use AlbumTest\Bootstrap;
use Album\Model\Song as SongModel;
use Loculus\Test\PHPUnit\Model\ModelTestCase;

class SongTest extends ModelTestCase
{
    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $this->model = new SongModel($this->em);

        parent::setUp();
    }


    public function testGetSongsCanBeAccessed()
    {
        $songs = $this->model->getSongs();
        $this->assertEquals(7, count($songs));
        // default order by
        $this->assertInstanceOf('Album\Entity\Song', $songs[0]);
        $this->assertEquals('Burning the Past', $songs[0]->name);
        $this->assertInstanceOf('DateTime', $songs[0]->duration);
        $this->assertEquals('00:02:48', $songs[0]->duration->format('H:i:s'));
    }

    public function testGetSongsByAlbumCanBeAccessed()
    {
        $songs = $this->model->getSongsByAlbum(6);
        $this->assertEquals(2, count($songs));
        // default order by
        $this->assertEquals(true, is_array($songs));
        $this->assertEquals('Burning the Past', $songs[0]->name);
        $this->assertInstanceOf('DateTime', $songs[0]->duration);
        $this->assertEquals('00:02:48', $songs[0]->duration->format('H:i:s'));
    }

    public function testGetSongsByAlbumCanBeAccessedWithNoSong()
    {
        $songs = $this->model->getSongsByAlbum(1);
        $this->assertEquals(0, count($songs));
        $this->assertEquals(true, is_array($songs));
    }

    public function testGetSongsByAlbumCanBeAccessedAsArray()
    {
        $songs = $this->model->getSongsByAlbum(6, null, Query::HYDRATE_ARRAY);
        $this->assertEquals(2, count($songs));
        // default order by
        $this->assertEquals(true, is_array($songs[0]));
        $this->assertEquals('Burning the Past', $songs[0]['name']);
        $this->assertEquals('00:02:48', $songs[0]['duration']->format('H:i:s'));
    }

    public function testGetSongsByAlbumOrderByNameCanBeAccessed()
    {
        $songs = $this->model->getSongsByAlbum(6, 'name');
        $this->assertEquals(2, count($songs));
        // order by name
        $this->assertInstanceOf('Album\Entity\Song', $songs[0]);
        $this->assertEquals('Burning the Past', $songs[0]->name);
        $this->assertInstanceOf('DateTime', $songs[0]->duration);
        $this->assertEquals('00:02:48', $songs[0]->duration->format('H:i:s'));
    }

    public function testGetSongsByAlbumOrderByDurationCanBeAccessed()
    {
        $songs = $this->model->getSongsByAlbum(6, 'duration');
        $this->assertEquals(2, count($songs));
        // order by name
        $this->assertInstanceOf('Album\Entity\Song', $songs[0]);
        $this->assertEquals('Swordplay', $songs[0]->name);
        $this->assertInstanceOf('DateTime', $songs[0]->duration);
        $this->assertEquals('00:02:01', $songs[0]->duration->format('H:i:s'));
    }

    public function testGetSongCanBeAccessed()
    {
        $song = $this->model->getSong(1);
        $this->assertInstanceOf('Album\Entity\Song', $song);
        $this->assertEquals('Burning the Past', $song->name);
        $this->assertInstanceOf('DateTime', $song->duration);
        $this->assertEquals('00:02:48', $song->duration->format('H:i:s'));
    }

    public function testGetSongCannotFindWithWrongId()
    {
        $song = $this->model->getSong(100);
        $this->assertNotInstanceOf('Album\Entity\Song', $song);
        $this->assertEquals(false, $song);
    }
}