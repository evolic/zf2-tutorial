<?php

namespace EvlCalendar\Controller;


use Loculus\Mvc\Controller\DefaultController,
    Loculus\Entity\Base as BaseEntity;
use Zend\Paginator\Adapter\ArrayAdapter,
    Zend\Paginator\Paginator,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel;
use Doctrine\ORM\EntityManager;
use EvlCalendar\Entity\Holiday,
    EvlCalendar\Form\HolidayForm,
    EvlCalendar\Model\Holidays as HolidaysModel,
    Loculus\Log;

class HolidaysController extends DefaultController
{
    const DEFAULT_ITEMS_PER_PAGE = 20;

    public function listAction()
    {
        $page = (int) $this->params()->fromRoute('page', 1);
        $orderBy = $this->params()->fromRoute('order_by', '');
        $year = (int) $this->params()->fromRoute('year', date('Y'));

        // setup navigation for breadcrumbs
        $this->setupNavigation();

        $model = new HolidaysModel($this->getEntityManager(), $this->getCacheAdapter());
        $list = $model->getHolidays($year, $orderBy);

        $paginator = new Paginator(new ArrayAdapter($list));
        $paginator->setCurrentPageNumber($page);
        $this->getCacheAdapter()->getOptions()->setNamespace('academic_years');

        $this->setupPaginator();

        $year = (int) date('Y');
        $prev_year = $year - 1;
        $next_year = $year + 1;

        $this->viewModel->setVariables(array(
            'prev_year' => $prev_year,
            'year' => $year,
            'next_year' => $next_year,
            'order_by' => $orderBy,
            'paginator' => $paginator,
        ));
        return $this->viewModel;
    }

    public function addAction()
    {
    }

    public function createAction()
    {
        $year = (int) $this->params()->fromRoute('year', date('Y'));

        $model = new HolidaysModel($this->getEntityManager(), $this->getCacheAdapter());
        $created = $model->createHolidaysForYear($year);

        if ($created) {
            $message = $this->translate('Successfully added %d holidays for a year: %d');
            $this->flashmessenger()->addSuccessMessage(sprintf(
                $message, $created, $year
            ));
        } else {
            $message = $this->translate('No new holiday was created for year: %d');
            $this->flashmessenger()->addSuccessMessage(sprintf($message, $year));
        }

        // Redirect to list of holidays
        return $this->redirect()->toRoute('holidays', array(
            'action' => 'list',
            'year' => $year,
        ));
    }

    public function calendarAction()
    {
        return $this->viewModel;
    }

    public function getEventsAction()
    {
        $model = new HolidaysModel($this->getEntityManager(), $this->getCacheAdapter());
        $events = $model->getHolidaysEvents();

        return new JsonModel(array(
            'events' => $events,
            'success' => true,
        ));
    }

    public function setupPaginator()
    {
        Paginator::setDefaultItemCountPerPage(self::DEFAULT_ITEMS_PER_PAGE);
        Paginator::setDefaultScrollingStyle('Sliding');
        Paginator::setCache($this->getCacheAdapter());
    }

    public function setupNavigation($holiday = null)
    {
        $this->getServiceLocator()->get('Zend\Log')->info(__NAMESPACE__ . '\\' . __CLASS__ . '::' . __FUNCTION__);
        $action = $this->params()->fromRoute('action', '');
        $this->getServiceLocator()->get('Zend\Log')->info("route action: " . $action);

        $navigation = $this->getServiceLocator()->get('Navigation');
        $page = $navigation->findOneBy('label', 'List of holidays');
        if ($page) {
            $this->getServiceLocator()->get('Zend\Log')->info('List of holidays');
            $page->setLabel($this->translate('List of holidays'));
        } else {
            $this->getServiceLocator()->get('Zend\Log')->info("page not found");
        }
    }
}