<?php
namespace AlbumTest\Controller;

use Zend\Http\Request as HttpRequest;
use Loculus\Test\PHPUnit\Controller\HttpControllerTestCase;

class SongControllerTest extends HttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfig.php'
        );

        parent::setUp();
    }


    public function testIndexActionCanBeAccessedWithNoSongs()
    {
        $this->dispatch('/en-US/song/10');

        $xpath = '/html/body/div[2]/p[2]';
        $this->assertXpathQueryContentContains($xpath, $this->translate('No song was found.'));

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('song');
    }

    public function testIndexActionCanBeAccessedWithSongs()
    {
        $this->dispatch('/en-US/song/6');

        $xpath = '/html/body/div[2]/p[2]';
        $this->assertNotXpathQueryContentContains($xpath, $this->translate('No song was found.'));

        $count = 2;
        $text = $this->translatePlural('Found %d song in album.', 'Found %d songs in album.', $count, 'album', 'en-US');
        $text = sprintf($text, $count);
        $this->assertXpathQueryContentContains($xpath, $text);

        $xpath = '/html/body/div[2]/table/tr';
        $this->assertXpathQueryCount($xpath, $count+1);

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('song');
    }

    public function testIndexActionWithNoAlbumId()
    {
        $this->dispatch('/en-US/song');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('song');
    }

    public function testIndexActionWithWrongAlbumId()
    {
        $this->dispatch('/en-US/song/100');

        $this->assertResponseStatusCode(404);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('song');
    }

    public function testIndexActionCanBeAccessedWithOrderByTitle()
    {
        $this->dispatch('/en-US/song/6,,name');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('song');
    }

    public function testAddActionCanBeAccessed()
    {
        $this->dispatch('/en-US/song/6/add');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('song');
    }

    public function testAddActionCannotBeAccessedWithNoAlbumId()
    {
        $this->dispatch('/en-US/song/add');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('song');
    }

    public function testAddActionCannotBeAccessedWithInvalidAlbumId()
    {
        $this->dispatch('/en-US/song/100/add');

        $this->assertResponseStatusCode(404);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('song');
    }

    public function testAddActionCannotInsertSongWithMissingData()
    {
        $post = array(
            'album_id' => 6,
            'position' => '',
            'name' => 'Swordplay',
            'duration' => '2:01',
            'disc' => 1
        );
        $this->dispatch('/en-US/song/6/add', HttpRequest::METHOD_POST, $post);

        $this->assertQueryContentContains('form ul li', "Value is required and can't be empty");

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('song');
    }

    public function testAddActionCannotInsertSongWithInvalidPositionNumber()
    {
        $post = array(
            'album_id' => 6,
            'position' => '0',
            'name' => 'Swordplay',
            'duration' => '2:01',
            'disc' => 1
        );
        $this->dispatch('/en-US/song/6/add', HttpRequest::METHOD_POST, $post);

        $this->assertQueryContentContains('form ul li', "Value is required and can't be empty");

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('song');
    }

    public function testAddActionCannotInsertSongWithInvalidData()
    {
        $post = array(
            'album_id' => 6,
            'position' => 'a',
            'name' => 'Swordplay',
            'duration' => '2:01',
            'disc' => 1
        );
        $this->dispatch('/en-US/song/6/add', HttpRequest::METHOD_POST, $post);

        $this->assertQueryContentContains('form ul li', "Value is required and can't be empty");

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('song');
    }

    public function testAddActionCanInsertSongWithValidData()
    {
        $post = array(
            'id' => '',
            'album_id' => 6,
            'position' => 3,
            'name' => 'Swordplay',
            'duration' => '2:01',
            'disc' => 1
        );
        $this->dispatch('/en-US/song/6/add', HttpRequest::METHOD_POST, $post);

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('song');
    }

    public function testIndexActionCheckIfThereIsNewSong()
    {
        $this->dispatch('/en-US/song/6');

        $xpath = '/html/body/div[2]/p[2]';
        $this->assertNotXpathQueryContentContains($xpath, $this->translate('No song was found.'));

        $count = 3;
        $text = $this->translatePlural('Found %d song in album.', 'Found %d songs in album.', $count, 'album', 'en-US');
        $text = sprintf($text, $count);
        $this->assertXpathQueryContentContains($xpath, $text);

        $xpath = '/html/body/div[2]/table/tr';
        $this->assertXpathQueryCount($xpath, $count+1);

        $xpath = '/html/body/div[2]/table/tr[4]/td[2]';
        $this->assertXpathQueryContentContains($xpath, 'Swordplay');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('song');
    }

    public function testEditActionCanBeAccessed()
    {
        $this->dispatch('/en-US/song/6/edit/8');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('song');
    }

    public function testEditActionWithNoSongId()
    {
        $this->dispatch('/en-US/song/6/edit');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('song');
    }

    public function testEditActionWithWrongAlbumId()
    {
        $this->dispatch('/en-US/song/100/edit/100');

        $this->assertResponseStatusCode(404);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('song');
    }

    public function testEditActionWithWrongSongId()
    {
        $this->dispatch('/en-US/song/6/edit/100');

        $this->assertResponseStatusCode(404);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('song');
    }

    public function testEditActionCannotUpdateWithInvalidData()
    {
        $post = array(
            'album_id' => 6,
            'id' => 2,
            'position' => 3,
            'name' => '',
            'duration' => '2:01',
            'disc' => 1
        );
        $this->dispatch('/en-US/song/6/edit/2', HttpRequest::METHOD_POST, $post);

        $this->assertQueryContentContains('form ul li', "Value is required and can't be empty");
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('song');
    }

    public function testEditActionCanUpdateWithValidData()
    {
        $post = array(
            'album_id' => 6,
            'id' => 2,
            'position' => 3,
            'name' => 'Swordplay',
            'duration' => '2:01',
            'disc' => 1
        );
        $this->dispatch('/en-US/song/6/edit/2', HttpRequest::METHOD_POST, $post);

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('edit');
        $this->assertMatchedRouteName('song');
    }

    public function testDeleteActionCanBeAccessed()
    {
        $this->dispatch('/en-US/song/6/delete/8');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('song');
    }

    public function testDeleteActionWithNoSong()
    {
        $this->dispatch('/en-US/song/6/delete');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('song');
    }

    public function testDeleteActionWithWrongAlbumId()
    {
        $this->dispatch('/en-US/song/100/delete/100');

        $this->assertResponseStatusCode(404);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('song');
    }

    public function testDeleteActionWithWrongSongId()
    {
        $this->dispatch('/en-US/song/6/delete/100');

        $this->assertResponseStatusCode(404);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('song');
    }

    public function testDeleteActionRedirectAfterDelete()
    {
        $post = array(
            'id' => 8,
            'del' => $this->translate('Yes'),
        );
        $this->dispatch('/en-US/song/6/delete/8', HttpRequest::METHOD_POST, $post);

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('delete');
        $this->assertMatchedRouteName('song');
    }

    public function testIndexActionCheckIfNewSongWasDeleted()
    {
        $this->dispatch('/en-US/song/6');

        $xpath = '/html/body/div[2]/p[2]';
        $this->assertNotXpathQueryContentContains($xpath, $this->translate('No song was found.'));

        $count = 2;
        $text = $this->translatePlural('Found %d song in album.', 'Found %d songs in album.', $count, 'album', 'en-US');
        $text = sprintf($text, $count);
        $this->assertXpathQueryContentContains($xpath, $text);

        $xpath = '/html/body/div[2]/table/tr';
        $this->assertXpathQueryCount($xpath, $count+1);

        $xpath = '/html/body/div[2]/table/tr[2]/td[2]';
        $this->assertXpathQueryContentContains($xpath, 'Burning the Past');

        $xpath = '/html/body/div[2]/table/tr[3]/td[2]';
        $this->assertXpathQueryContentContains($xpath, 'Swordplay'); // updated in testEditActionCanUpdateWithValidData

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName('album/song');
        $this->assertControllerClass('SongController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('song');
    }

    public function tearDown()
    {
        $sm = $this->getApplicationServiceLocator();
        $em = $sm->get('doctrine.entitymanager.orm_default');
        $dbh = $em->getConnection();
        $result = $dbh->exec("UPDATE sqlite_sequence SET seq = 7 WHERE name='songs';");

        parent::tearDown();
    }
}