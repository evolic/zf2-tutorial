<?php

namespace AlbumTest\Form;

use AlbumTest\Bootstrap;
use Album\Entity\Album;
use Album\Form\AlbumForm;
use Loculus\Test\PHPUnit\Form\FormTestCase;

class AlbumFormTest extends FormTestCase
{
    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $this->entity = new Album();
        $this->form = new AlbumForm();
        $this->form->setInputFilter($this->entity->getInputFilter());

        parent::setUp();
    }


    public function testCanInsertNewRecord()
    {
        $data = array(
            'artist' => 'Led Zeppelin',
            'title' => 'Led Zeppelin III',
            'discs' => 1
        );
        $this->form->setData($data);
        $this->assertTrue($this->form->isValid());
    }

    public function testCannotInsertNewRecordWithInvalidData()
    {
        $data = array(
            'artist' => '',
            'title' => 'Led Zeppelin III',
            'discs' => 1
        );
        $this->form->setData($data);
        $this->assertFalse($this->form->isValid());
        $this->assertEquals(1, count($this->form->get('artist')->getMessages()));
    }

    public function testCanUpdateExistingRecord()
    {
        $album = $this->em->find('Album\Entity\Album', 8);
        $data = array(
            'artist' => 'Jem',
            'title' => 'Finally woken',
            'discs' => 1
        );
        $this->form->setData($data);
        $this->assertTrue($this->form->isValid());
    }

    public function testCannnotUpdateExistingRecordWithInvalidData()
    {
        $album = $this->em->find('Album\Entity\Album', 8);
        $data = array(
            'artist' => 'Jem',
            'title' => '',
            'discs' => 1
        );
        $this->form->setData($data);
        $this->assertFalse($this->form->isValid());
        $this->assertEquals(1, count($this->form->get('title')->getMessages()));
    }
}