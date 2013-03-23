<?php

namespace Album\Controller;

use Loculus\Mvc\Controller\DefaultController,
    Zend\View\Model\ViewModel,
    Album\Form\SongForm,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Query\Expr\Join,
    Album\Entity\Song,
    Album\Model\Song as SongModel,
    Album\Entity\Album,
    Album\Model\Album as AlbumModel,
    Loculus\Log;

class SongController extends DefaultController
{
    public function indexAction()
    {
        $album_id = (int) $this->params()->fromRoute('album_id', 0);
        $orderBy = $this->params()->fromRoute('order_by', '');

        if (!$album_id) {
            return $this->redirect()->toRoute('album');
        }

        $albumModel = new AlbumModel($this->getEntityManager());
        $album = $albumModel->getAlbum($album_id);
        if (!$album) {
            return $this->redirect()->toRoute('album');
        }

        $songModel = new SongModel($this->getEntityManager());
        $songModel->setServiceLocator($this->getServiceLocator());
        $songs = $songModel->getSongsByAlbum($album_id, $orderBy);

        return new ViewModel(array(
            'album' => $album,
            'songs' => $songs
        ));
    }

    public function addAction()
    {
        $album_id = (int) $this->params()->fromRoute('album_id', 0);
        if (!$album_id) {
            return $this->redirect()->toRoute('album');
        }

        $albumModel = new AlbumModel($this->getEntityManager());
        $album = $albumModel->getAlbum($album_id);
        if (!$album) {
            return $this->redirect()->toRoute('album');
        }

        $form = new SongForm('song-add', $album->discs);
        $form->get('submit')->setValue('Add');
        $form->get('album_id')->setValue($album->id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $song = new Song();

            $form->setInputFilter($song->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['album'] = $album;
                $song->populate($data);
                $this->getEntityManager()->persist($song);
                $this->getEntityManager()->flush();

                // Redirect to list of songs
                return $this->redirect()->toRoute('song',array('album_id' => $album->id));
            }
        }
        return array(
            'form' => $form,
            'album' => $album
        );
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('song', array(
                'action' => 'add'
            ));
        }

        $songModel = new SongModel($this->getEntityManager());
        $songModel->setServiceLocator($this->getServiceLocator());
        $song = $songModel->getSong($id);
        if (!$song) {
            return $this->redirect()->toRoute('album');
        }

        $albumModel = new AlbumModel($this->getEntityManager());
        $album = $albumModel->getAlbum($song->album_id);
        if (!$album) {
            return $this->redirect()->toRoute('album');
        }

        $form = new SongForm();
        $form->setBindOnValidate(false);
        $form->bind($song);
        $form->get('submit')->setValue('Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $post['album'] = $album;
            $form->setData($post);

            if ($form->isValid()) {
                $form->bindValues();

                // @FIXME Fix problem with missing relationship mapping
                $song->album = $album;
                $this->getEntityManager()->flush();

                // Redirect to list of songs
                return $this->redirect()->toRoute('song',array('album_id' => $song->album_id));
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'album' => $album
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $songModel = new SongModel($this->getEntityManager());
        $songModel->setServiceLocator($this->getServiceLocator());
        $song = $songModel->getSong($id);
        if (!$song) {
            return $this->redirect()->toRoute('album');
        }

        $albumModel = new AlbumModel($this->getEntityManager());
        $album = $albumModel->getAlbum($song->album_id);
        if (!$album) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $this->getEntityManager()->remove($song);
                $this->getEntityManager()->flush();
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('song', array(
                'album_id' => $song->album_id
            ));
        }

        return array(
            'id' => $id,
            'album' => $album,
            'song' => $song
        );
    }
}
