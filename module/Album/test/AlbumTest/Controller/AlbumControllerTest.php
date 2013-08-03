<?php
namespace AlbumTest\Controller;

use Zend\Http\Request as HttpRequest;
use Loculus\Test\PHPUnit\Controller\HttpControllerTestCase;

class AlbumControllerTest extends HttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfig.php'
        );

        parent::setUp();
    }


    public function testHomeRouteCanBeAccessed()
    {
        $this->dispatch('/en-US');

//         $this->assertEquals('<html>', $this->getApplication()->getResponse()->getBody());
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('home');
    }

    public function testAddActionCanBeAccessed()
    {
        $this->dispatch('/en-US/album/add');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('album');
    }

    public function testAddActionCannotInsertInvalidAlbum()
    {
        $post = array(
            'artist' => 'Led Zeppelin',
            'title' => '',
            'discs' => 1
        );
        $this->dispatch('/en-US/album/add', HttpRequest::METHOD_POST, $post);

        $this->assertQueryContentContains('form ul li', "Value is required and can't be empty");
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('album');
    }

    public function testAddActionCanInsertNewAlbum()
    {
        $post = array(
            'id' => '',
            'artist' => 'Led Zeppelin',
            'title' => 'Led Zeppelin III',
            'discs' => 1
        );
        $this->dispatch('/en-US/album/add', HttpRequest::METHOD_POST, $post);

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('album');
    }

    /**
     * @depends testAddActionCanInsertNewAlbum
     */
    public function testIndexActionCanCheckInsertedAlbum()
    {
        $this->dispatch('/en-US/album');

        $this->assertResponseStatusCode(200);
        $xpath = '/html/body/div[2]/table/tr[12]/td[2]/a';
        $this->assertXpathQueryContentContains($xpath, 'Led Zeppelin III');

//         $this->assertEquals('<html>', $this->getApplication()->getResponse()->getBody());

        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('album');
    }

    public function testEditActionCanBeAccessed()
    {
        $this->dispatch('/en-US/album/edit/10');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('album');
    }

    public function testEditActionCannotUpdateWithInvalidData()
    {
        $post = array(
            'artist' => 'Dido',
            'title' => '',
            'discs' => 1
        );
        $this->dispatch('/en-US/album/edit/10', HttpRequest::METHOD_POST, $post);

        $this->assertQueryContentContains('form ul li', "Value is required and can't be empty");
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('album');
    }

    public function testEditActionRedirectAfterUpdate()
    {
        $post = array(
            'id' => 10,
            'artist' => 'Dido',
            'title' => 'No Angel',
            'discs' => 1
        );
        $this->dispatch('/en-US/album/edit/10', HttpRequest::METHOD_POST, $post);

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('album');
    }

    public function testEditActionBadRequestCausedByMissingId()
    {
        $this->dispatch('/en-US/album/edit');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('album');
    }

    /**
     * @depends testAddActionCanInsertNewAlbum
     */
    public function testDeleteActionCanBeAccessed()
    {
        $this->dispatch('/en-US/album/delete/11');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('album');
    }

    /**
     * @depends testAddActionCanInsertNewAlbum
     */
    public function testDeleteActionRedirectAfterDelete()
    {
        $post = array(
            'id' => 11,
            'del' => $this->translate('Yes'),
        );
        $this->dispatch('/en-US/album/delete/11', HttpRequest::METHOD_POST, $post);

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('album');
    }

    public function testDeleteActionBadRequestCausedByMissingId()
    {
        $this->dispatch('/en-US/album/delete');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('album');
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/en-US/album/index');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('album');
    }

    public function testIndexActionCanBeAccessedWithOrderByTitle()
    {
        $this->dispatch('/en-US/album,,title');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/album');
        $this->assertControllerClass('AlbumController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('album');
    }


    public function tearDown()
    {
        $sm = $this->getApplicationServiceLocator();
        $em = $sm->get('doctrine.entitymanager.orm_default');
        $dbh = $em->getConnection();
        $result = $dbh->exec("UPDATE sqlite_sequence SET seq = 10 WHERE name='album';");

        parent::tearDown();
    }
}
