<?php

namespace XfnRestaurant\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OrderLunchForm extends Form implements InputFilterProviderInterface, ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;


    /**
     * Form building
     */
    public function init()
    {
        // we want to ignore the name passed
        parent::__construct('order-lunch-form');
        $this->setAttribute('method', 'post');

        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $translator    = $this->getServiceLocator()->get('translator');

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'main-course',
            'options' => array(
                'label' => $translator->translate('Main course', 'XfnRestaurant'),
                'object_manager' => $entityManager,
                'target_class' => 'XfnRestaurant\Entity\Meal',
                'label_generator' => function($targetEntity) {
                    return $targetEntity->getName() . ' - ' . $targetEntity->getPrice() . ' [' . $targetEntity->getCuisine()->getName() . ' cuisine]' . ' PLN';
                },
                'empty_option' => $translator->translate('Choose the main course', 'XfnRestaurant'),
            ),
            'attributes' => array(
                'id' => 'main-course',
                'class' => ''
            ),
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'dessert',
            'options' => array(
                'label' => $translator->translate('Dessert', 'XfnRestaurant'),
                'object_manager' => $entityManager,
                'target_class' => 'XfnRestaurant\Entity\Dessert',
                'label_generator' => function($targetEntity) {
                    return $targetEntity->getName() . ' - ' . $targetEntity->getPrice() . ' PLN';
                },
                'empty_option' => $translator->translate('Choose a dessert', 'XfnRestaurant'),
            ),
            'attributes' => array(
                'id' => 'dessert',
                'class' => ''
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
                'id' => 'submitbutton',
            ),
        ));
    }

    /**
     * Input filter and converter specification.
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $firephp = \FirePHP::getInstance();
        $firephp->info(__METHOD__);

        return array(
            'main-course' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ),
            'dessert' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ),
        );
    }

    /**
     * Method used to inject ServiceLocator.
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Method used to obtain injected ServiceLocator
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getServiceLocator();
    }
}