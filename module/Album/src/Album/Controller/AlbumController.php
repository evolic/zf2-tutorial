<?php

namespace Album\Controller;

use Zend\View\Model\ViewModel,
    Album\Form\AlbumForm,
    Album\Entity\Album,
    Album\Model\Album as AlbumModel,
    Doctrine\ORM\EntityManager,
    Loculus\MVC\Controller\DefaultController;

class AlbumController extends DefaultController
{
    public function indexAction()
    {
        $orderBy = $this->params()->fromRoute('order_by', '');
        $model = new AlbumModel($this->getEntityManager());
        $albums = $model->getAlbums($orderBy);

        return new ViewModel(array(
            'albums' => $albums
        ));
    }

    public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $album->populate($form->getData());
                $this->getEntityManager()->persist($album);
                $this->getEntityManager()->flush();

                $this->flashmessenger()->addSuccessMessage(sprintf('Added new album `%s`', $album->title));

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        if (!$id) {
            return $this->redirect()->toRoute('album', array('action'=>'add'));
        }

        $model = new AlbumModel($this->getEntityManager());
        $album = $model->getAlbum($id);

        $form = new AlbumForm();
        $form->setBindOnValidate(false);
        $form->bind($album);
        $form->get('submit')->setValue('Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $form->bindValues();
                $this->getEntityManager()->flush();

                $this->flashmessenger()->addSuccessMessage(sprintf('Updating album `%s` successfully completed', $album->title));

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $model = new AlbumModel($this->getEntityManager());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');

                $model = new AlbumModel($this->getEntityManager());
                $album = $model->getAlbum($id);

                if ($album) {
                    $this->getEntityManager()->remove($album);
                    $this->getEntityManager()->flush();
                }
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album', array(
                'action' => 'index',
            ));
        }

        return array(
            'id' => $id,
            'album' => $model->getAlbum($id)
        );
    }
}