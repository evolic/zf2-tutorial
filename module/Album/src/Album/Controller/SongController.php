<?php

namespace Album\Controller;

use Zend\View\Model\ViewModel,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Query\Expr\Join,
    Album\Entity\Album,
    Album\Entity\Song,
    Album\Form\SongForm,
    Album\Model\Album as AlbumModel,
    Album\Model\Song as SongModel,
    Loculus\Mvc\Controller\DefaultController;

class SongController extends DefaultController
{
    public function indexAction()
    {
        $locale = $this->params()->fromRoute('locale');
        $album_id = (int) $this->params()->fromRoute('album_id', 0);
        $orderBy = $this->params()->fromRoute('order_by', '');

        if (!$album_id || !$locale) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return $this->viewModel;
        }

        $albumModel = new AlbumModel($this->getEntityManager());
        $album = $albumModel->getAlbum($album_id);

        if (!$album) {
            $this->getEvent()->getResponse()->setStatusCode(404);
            return $this->viewModel;
        }

        $songModel = new SongModel($this->getEntityManager());
        $songModel->setServiceLocator($this->getServiceLocator());
        $songs = $songModel->getSongsByAlbum($album_id, $orderBy);

        $this->viewModel->setVariables(array(
            'album' => $album,
            'songs' => $songs
        ));
        return $this->viewModel;
    }

    public function addAction()
    {
        $locale = $this->params()->fromRoute('locale');
        $album_id = (int) $this->params()->fromRoute('album_id', 0);

        if (!$album_id || !$locale) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return $this->viewModel;
        }

        $albumModel = new AlbumModel($this->getEntityManager());
        $album = $albumModel->getAlbum($album_id);

        if (!$album) {
            $this->getEvent()->getResponse()->setStatusCode(404);
            return $this->viewModel;
        }

        $form = new SongForm('song-add', $album->discs);
        $form->get('submit')->setValue($this->translate('Add'));
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
                return $this->redirect()->toRoute('song', array(
                    'locale' => $locale,
                    'album_id' => $album->id
                ));
            }
        }
        $this->viewModel->setVariables(array(
            'form' => $form,
            'album' => $album
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $locale = $this->params()->fromRoute('locale');
        $album_id = (int) $this->params()->fromRoute('album_id', 0);
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id || !$album_id || !$locale) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return $this->viewModel;
        }

        $albumModel = new AlbumModel($this->getEntityManager());
        $album = $albumModel->getAlbum($album_id);

        if (!$album) {
            $this->getEvent()->getResponse()->setStatusCode(404);
            return $this->viewModel;
        }

        $songModel = new SongModel($this->getEntityManager());
        $songModel->setServiceLocator($this->getServiceLocator());
        $song = $songModel->getSong($id, $album_id);

        if (!$song) {
              $this->getEvent()->getResponse()->setStatusCode(404);
            return $this->viewModel;
        }

        $form = new SongForm();
        $form->setBindOnValidate(false);
        $form->bind($song);
        $form->get('submit')->setValue($this->translate('Edit'));

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
                return $this->redirect()->toRoute('song', array(
                    'locale' => $locale,
                    'album_id' => $song->album_id
                ));
            }
        }

        $this->viewModel->setVariables(array(
            'id' => $id,
            'form' => $form,
            'album' => $album
        ));
        return $this->viewModel;
    }

    public function deleteAction()
    {
        $locale = $this->params()->fromRoute('locale');
        $album_id = (int) $this->params()->fromRoute('album_id', 0);
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id || !$album_id || !$locale) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return $this->viewModel;
        }

        $albumModel = new AlbumModel($this->getEntityManager());
        $album = $albumModel->getAlbum($album_id);

        if (!$album) {
            $this->getEvent()->getResponse()->setStatusCode(404);
            return $this->viewModel;
        }

        $songModel = new SongModel($this->getEntityManager());
        $songModel->setServiceLocator($this->getServiceLocator());
        $song = $songModel->getSong($id, $album_id);

        if (!$song) {
            $this->getEvent()->getResponse()->setStatusCode(404);
            return $this->viewModel;
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == $this->translate('Yes')) {
                $this->getEntityManager()->remove($song);
                $this->getEntityManager()->flush();
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('song', array(
                'locale' => $locale,
                'album_id' => $song->album_id
            ));
        }

        $this->viewModel->setVariables(array(
            'id' => $id,
            'album' => $album,
            'song' => $song
        ));
        return $this->viewModel;
    }
}
