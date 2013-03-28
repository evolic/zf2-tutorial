<?php

namespace AlbumTest\Model;


use AlbumTest\Bootstrap;
use Album\Model\Album as AlbumModel;
use Loculus\Test\PHPUnit\Model\ModelTestCase;

class AlbumTest extends ModelTestCase
{
    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $this->model = new AlbumModel($this->em);

        parent::setUp();
    }


    public function testGetAlbumsCanBeAccessed()
    {
        $albums = $this->model->getAlbums();
        $this->assertEquals(10, count($albums));
        // default order by
        $this->assertEquals('The Military Wives', $albums[0]->artist);
        $this->assertEquals('In My Dreams', $albums[0]->title);
    }

    public function testGetAlbumsByArtistCanBeAccessed()
    {
        $albums = $this->model->getAlbums('artist');
        $this->assertEquals(10, count($albums));
        // order by artist
        $this->assertEquals('Adele', $albums[0]->artist);
        $this->assertEquals('21', $albums[0]->title);
    }

    public function testGetAlbumsByAlbumTitleCanBeAccessed()
    {
        $albums = $this->model->getAlbums('title');
        $this->assertEquals(10, count($albums));
        // order by artist
        $this->assertEquals('Adele', $albums[0]->artist);
        $this->assertEquals('21', $albums[0]->title);
    }

    public function testGetAlbumCanBeAccessed()
    {
        $album = $this->model->getAlbum(2);
        $this->assertInstanceOf('Album\Entity\Album', $album);
        $this->assertEquals('Adele', $album->artist);
        $this->assertEquals('21', $album->title);
    }

    public function testGetAlbumCannotFindWithWrongId()
    {
        $album = $this->model->getAlbum(100);
        $this->assertNotInstanceOf('Album\Entity\Album', $album);
        $this->assertEquals(false, $album);
    }
}