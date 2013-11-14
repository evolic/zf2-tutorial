<?php

namespace XfnRestaurant\Controller;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\InArray;
use Zend\Validator\NotEmpty;
use Zend\Validator\ValidatorChain;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use XfnRestaurant\Entity\Order;
use XfnRestaurant\Form\OrderMainForm;
use XfnRestaurant\Form\OrderDrinkForm;
use XfnRestaurant\Form\OrderLunchForm;
use XfnRestaurant\Service\OrdersService;
use Loculus\Mvc\Controller\DefaultController;

class OrdersController extends DefaultController
{
    /**
     *
     * @var OrderDrinkForm
     */
    private $orderDrinkForm;

    /**
     *
     * @var OrderLunchForm
     */
    private $orderLunchForm;

    /**
     *
     * @var OrdersService
     */
    private $ordersService;


    public function indexAction()
    {
        $locale = $this->params()->fromRoute('locale');
        $orderBy = $this->params()->fromRoute('order_by', '');
        $limit = 20;

        $criteria = array(
            'limit' => $limit,
            'order_by' => $orderBy,
        );

        $orders = $this->getOrdersService()->getOrdersRepository()->getOrders(
            $criteria, Query::HYDRATE_ARRAY
        );

        $messages = $this->FlashMessenger()->getSuccessMessages();
        $errors = $this->FlashMessenger()->getErrorMessages();

        $this->viewModel->setVariables(array(
            'orders' => $orders,
            'messages' => $messages,
            'errors' => $errors,
        ));
        return $this->viewModel;
    }

    public function detailsAction()
    {
        $locale = $this->params()->fromRoute('locale');
        $id = (int) $this->params()->fromRoute('id');

        if (!$id || !$locale) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return $this->viewModel;
        }

        $order = $this->getOrdersService()->getOrdersRepository()->find($id);

        if (!$order) {
            $this->getEvent()->getResponse()->setStatusCode(404);
            return $this->viewModel;
        }

        $this->viewModel->setVariables(array(
            'id' => $id,
            'order' => $order
        ));

        return $this->viewModel;
    }

    public function makeAction()
    {
        $locale = $this->params()->fromRoute('locale');

        if (!$locale) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return $this->viewModel;
        }

        $form = new OrderMainForm();

        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add($factory->createInput(array(
            'name'       => 'type',
            'required'   => true,
            'filters' => array(
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Please choose one of the above options',
                        ),
                    ),
                ),
            ),
        )));

        $form->setInputFilter($inputFilter);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $values = $form->getData();

                switch ($values['type']) {
                    case Order::TYPE_LUNCH:
                        return $this->redirect()->toRoute('restaurant/orders/order-lunch', array('locale' => $locale));
                    case Order::TYPE_DRINK:
                        return $this->redirect()->toRoute('restaurant/orders/order-drink', array('locale' => $locale));
                }
            }
        }

        $this->viewModel->setVariables(array(
            'form' => $form,
        ));

        return $this->viewModel;
    }

    public function orderDrinkAction()
    {
        $locale = $this->params()->fromRoute('locale');

        if (!$locale) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return $this->viewModel;
        }

        $form = $this->getOrderDrinkForm();

        // create new validator chain
        $newValidatorChain = new ValidatorChain;
        // loop through all validators of the validator chained currently attached to the element
        foreach ($form->getInputFilter()->get('drink')->getValidatorChain()->getValidators() as $validator) {
            $instance = $validator['instance'];
            if ($instance instanceof InArray) {
                $instance->setMessages(array(
                    InArray::NOT_IN_ARRAY => 'Please choose a drink',
                ));
                $newValidatorChain->addValidator($instance, $validator['breakChainOnFailure']);
            }
        }
        // replace the old validator chain on the element
        $form->getInputFilter()->get('drink')->setValidatorChain($newValidatorChain);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $values = $form->getData();

                if ($this->getOrdersService()->createNewDrinkOrder($values)) {
                    $this->flashMessenger()->addSuccessMessage("New order for a drink has been successfully added.");
                } else {
                    $this->flashMessenger()->addErrorMessage("Exception occurred while trying to create new order for a drink.");
                }

                // Redirect to list of orders
                return $this->redirect()->toRoute('restaurant/orders', array('locale' => $locale));
            }
        }

        $this->viewModel->setVariables(array(
            'form' => $form,
        ));

        return $this->viewModel;
    }

    public function orderLunchAction()
    {
        $locale = $this->params()->fromRoute('locale');

        if (!$locale) {
            $this->getEvent()->getResponse()->setStatusCode(400);
            return $this->viewModel;
        }

        $form = $this->getOrderLunchForm();

        $selects = array(
            'main-course' => 'Please choose the main course',
            'dessert' => 'Please choose a dessert',
        );

        foreach ($selects as $select => $message) {
            // create new validator chain
            $newValidatorChain = new ValidatorChain;
            // loop through all validators of the validator chained currently attached to the element
            foreach ($form->getInputFilter()->get($select)->getValidatorChain()->getValidators() as $validator) {
                $instance = $validator['instance'];
                if ($instance instanceof InArray) {
                    $instance->setMessages(array(
                        InArray::NOT_IN_ARRAY => $message,
                    ));
                    $newValidatorChain->addValidator($instance, $validator['breakChainOnFailure']);
                }
            }
            // replace the old validator chain on the element
            $form->getInputFilter()->get($select)->setValidatorChain($newValidatorChain);
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $values = $form->getData();

                if ($this->getOrdersService()->createNewLunchOrder($values)) {
                    $this->flashMessenger()->addSuccessMessage("New order for a lunch has been successfully added.");
                } else {
                    $this->flashMessenger()->addErrorMessage("Exception occurred while trying to create new order for a lunch.");
                }

                // Redirect to list of orders
                return $this->redirect()->toRoute('restaurant/orders', array('locale' => $locale));
            }
        }

        $this->viewModel->setVariables(array(
            'form' => $form,
        ));

        return $this->viewModel;
    }


    /**
     * Method used to inject form handling a drink ordering.
     *
     * @param OrderDrinkForm $orderDrinkForm
     */
    public function setOrderDrinkForm(OrderDrinkForm $orderDrinkForm)
    {
        $this->orderDrinkForm = $orderDrinkForm;
    }

    /**
     * Method used to obtain form handling a drink ordering.
     *
     * @return OrderDrinkForm
     */
    public function getOrderDrinkForm()
    {
        return $this->orderDrinkForm;
    }

    /**
     * Method used to inject form handling a lunch ordering.
     *
     * @param OrderLunchForm $orderLunchForm
     */
    public function setOrderLunchForm(OrderLunchForm $orderLunchForm)
    {
        $this->orderLunchForm = $orderLunchForm;
    }

    /**
     * Method used to obtain form handling a lunch ordering.
     *
     * @return OrderLunchForm
     */
    public function getOrderLunchForm()
    {
        return $this->orderLunchForm;
    }

    /**
     * Method used to inject orders service.
     *
     * @param OrdersService $ordersService
     */
    public function setOrdersService(OrdersService $ordersService)
    {
        $this->ordersService = $ordersService;
    }

    /**
     * Method used to obtain orders service.
     *
     * @return OrdersService
     */
    public function getOrdersService()
    {
        return $this->ordersService;
    }
}