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
        $locale = $this->params()->fromRoute('locale');
        $orderBy = $this->params()->fromRoute('order_by', '');

        $model = new AlbumModel($this->getEntityManager());
        $albums = $model->getAlbums($orderBy);

        $this->viewModel->setVariables(array(
            'albums' => $albums
        ));
        return $this->viewModel;
    }

    public function addAction()
    {
        $locale = $this->params()->fromRoute('locale');

        $form = new AlbumForm();
        $form->get('submit')->setValue($this->translate('Add'));

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
                return $this->redirect()->toRoute('album', array('locale' => $locale));
            }
        }

        $this->viewModel->setVariables(array(
            'form' => $form
        ));

        return $this->viewModel;
    }

    public function editAction()
    {
        $locale = $this->params()->fromRoute('locale');
        $id = (int) $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('album', array('action'=>'add', 'locale' => $locale));
        }

        $model = new AlbumModel($this->getEntityManager());
        $album = $model->getAlbum($id);

        $form = new AlbumForm();
        $form->setBindOnValidate(false);
        $form->bind($album);
        $form->get('submit')->setValue($this->translate('Save'));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $form->bindValues();
                $this->getEntityManager()->flush();

                $this->flashmessenger()->addSuccessMessage(sprintf('Updating album `%s` successfully completed', $album->title));

                // Redirect to list of albums
                return $this->redirect()->toRoute('album', array('locale' => $locale));
            }
        }

        $this->viewModel->setVariables(array(
            'id' => $id,
            'form' => $form,
        ));
        return $this->viewModel;
    }

    public function deleteAction()
    {
        $locale = $this->params()->fromRoute('locale');
        $id = (int) $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('album', array('locale' => $locale));
        }

        $model = new AlbumModel($this->getEntityManager());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == $this->translate('Yes')) {
                $id = (int) $request->getPost('id');

                $model = new AlbumModel($this->getEntityManager());
                $album = $model->getAlbum($id);

                if ($album) {
                    $this->getEntityManager()->remove($album);
                    $this->getEntityManager()->flush();
                }
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album', array('locale' => $locale));
        }

        $this->viewModel->setVariables(array(
            'id' => $id,
            'album' => $model->getAlbum($id)
        ));
        return $this->viewModel;
    }
}