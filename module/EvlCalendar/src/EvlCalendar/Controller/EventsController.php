<?php

namespace EvlCalendar\Controller;


use Loculus\Mvc\Controller\DefaultController,
    Loculus\Entity\Base as BaseEntity;
use Zend\Paginator\Adapter\ArrayAdapter,
    Zend\Paginator\Paginator,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel;
use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Query;
use EvlCalendar\Entity\Event,
    EvlCalendar\Form\EventForm,
    EvlCalendar\Model\Events as EventsModel,
    Loculus\Log;

class EventsController extends DefaultController
{
    const DEFAULT_ITEMS_PER_PAGE = 20;

    public function addAction()
    {
    }

    public function calendarAction()
    {
        return $this->viewModel;
    }

    public function getEventsAction()
    {
        $start = (int) $this->params()->fromQuery('start', 0);
        $end   = (int) $this->params()->fromQuery('end', 0);

        $starting_at = date('Y-m-d H:i:s', $start);
        $ending_at   = date('Y-m-d H:i:s', $end);

        $model = new EventsModel($this->getEntityManager(), $this->getCacheAdapter());
        $events = $model->getEvents($starting_at, $ending_at);

        return new JsonModel(array(
            'events' => $events,
            'success' => true,
        ));
    }

    public function addEventAction()
    {
        $form = new EventForm();

        $success = false;
        $message = 'Bad request';
        $ts      = $this->params()->fromPost('ts', 0);

        if (!$ts) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return new JsonModel(array(
                'message' => $message,
                'success' => false,
            ));
        }

        $id      = 0;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $event = new Event();
            $form->setInputFilter($event->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $event->populate($data);
                $this->getEntityManager()->persist($event);
                $this->getEntityManager()->flush();

                $success = true;
                $id      = (int) $event->id;
                $message = sprintf('Added new event `%s`', $event->name);
            } else {
                $message = 'Invalid data';
            }
        } else {
            $this->getEvent()->getResponse()->setStatusCode(400);
        }

        return new JsonModel(array(
            'message' => $message,
            'success' => $success,
            'ts'      => $ts,
            'id'      => $id,
        ));
    }


    public function updateEventAction()
    {
        $success = false;
        $message = 'Bad request';
        $ts      = $this->params()->fromPost('ts', 0);
        $id      = (int) $this->params()->fromPost('id', 0);

        if (!$id || !$ts) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return new JsonModel(array(
                'message' => $message,
                'success' => false,
            ));
        }

        $request = $this->getRequest();

        $model = new EventsModel($this->getEntityManager(), $this->getCacheAdapter());
        $event = $model->getEvent($id);

        if (!$event) {
            $message = 'Not found';
            $this->getEvent()->getResponse()->setStatusCode(404);
            return new JsonModel(array(
                'message' => $message,
                'success' => false,
            ));
        }

        $form = new EventForm();
        $form->setBindOnValidate(false);
        $form->bind($event);

        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setData($post);

            if ($form->isValid()) {
                $form->bindValues();
                $this->getEntityManager()->flush();

                $success = true;
                $message = sprintf('Updated event `%s`', $event->name);
            } else {
                $message = 'Invalid data';
            }
        } else {
            $this->getEvent()->getResponse()->setStatusCode(400);
        }

        return new JsonModel(array(
            'message' => $message,
            'success' => $success,
            'ts'      => $ts,
            'id'      => $id,
        ));
    }
}