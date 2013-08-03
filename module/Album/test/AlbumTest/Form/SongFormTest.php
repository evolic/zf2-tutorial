<?php

namespace AlbumTest\Form;

use AlbumTest\Bootstrap;
use Album\Entity\Album;
use Album\Entity\Song;
use Album\Form\SongForm;
use Loculus\Test\PHPUnit\Form\FormTestCase;

class SongFormTest extends FormTestCase
{
    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $this->entity = new Song();
        $this->form = new SongForm();
        $this->form->setInputFilter($this->entity->getInputFilter());

        parent::setUp();
    }


    public function testCouldInsertNewRecordWithValidData()
    {
        $album_id = 6;
        $album = $this->em->getRepository('Album\Entity\Album')->find($album_id);

        $song = new Song();

        $data = array(
            'id' => '',
            'album_id' => $album_id,
            'album' => $album,
            'position' => 3,
            'name' => 'Swordplay',
            'duration' => '00:02:01',
            'disc' => 1
        );

        $this->form->setInputFilter($song->getInputFilter());
        $this->form->setData($data);

        $this->assertEquals(array(), $this->form->getMessages());
        $this->assertTrue($this->form->isValid());
    }

    public function testCannotInsertNewRecordWithInvalidData()
    {
        $album_id = 6;
        $album = $this->em->getRepository('Album\Entity\Album')->find($album_id);

        $song = new Song();

        $data = array(
            'album_id' => $album_id,
            'album' => $album,
            'id' => null,
            'position' => 3,
            'name' => 'Swordplay',
            'duration' => '',
            'disc' => 1
        );

        $this->form->setInputFilter($song->getInputFilter());
        $this->form->setData($data);

        $this->assertFalse($this->form->isValid());
        $this->assertEquals(1, count($this->form->get('duration')->getMessages()));
    }

    public function testCanUpdateExistingRecord()
    {
        $song_id = 6;
        $song = $this->em->getRepository('Album\Entity\Song')->find($song_id);

        $data = array(
            'album_id' => $song->album_id,
            'album' => $song->album,
            'id' => $song_id,
            'position' => 3,
            'name' => 'Swordplay',
            'duration' => '00:02:01',
            'disc' => 1
        );

        $this->form->setInputFilter($song->getInputFilter());
        $this->form->setData($data);

        $this->assertEquals(array(), $this->form->getMessages());
        $this->assertTrue($this->form->isValid());
    }

    public function testCannnotUpdateExistingRecordWithInvalidData()
    {
        $song_id = 6;
        $song = $this->em->getRepository('Album\Entity\Song')->find($song_id);
        $data = array(
            'album_id' => $song->album_id,
            'album' => $song->album,
            'id' => $song_id,
            'position' => 3,
            'name' => '',
            'duration' => '00:02:01',
            'disc' => 1
        );
        $this->form->setData($data);
        $this->assertFalse($this->form->isValid());
        $this->assertEquals(1, count($this->form->get('name')->getMessages()));
    }
}