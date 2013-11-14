<?php

namespace XfnRestaurant\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OrderDrinkForm extends Form implements InputFilterProviderInterface, ServiceLocatorAwareInterface
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
        parent::__construct('order-drink-form');
        $this->setAttribute('method', 'post');

        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $translator    = $this->getServiceLocator()->get('translator');

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'drink',
            'options' => array(
                'label' => $translator->translate('Drink', 'XfnRestaurant'),
                'object_manager' => $entityManager,
                'target_class' => 'XfnRestaurant\Entity\Drink',
                'label_generator' => function($targetEntity) {
                    return $targetEntity->getName() . ' - ' . $targetEntity->getPrice() . ' PLN';
                },
                'empty_option' => $translator->translate('Choose a drink', 'XfnRestaurant'),
            ),
            'attributes' => array(
                'id' => 'drink',
                'class' => ''
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'ice-cubes',
            'options' => array(
                'label' => 'Ice cubes',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 'no'
            ),
            'attributes' => array(
                'id' => 'ice-cubes',
                'class' => ''
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'lemon',
            'options' => array(
                'label' => 'Lemon',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 'no'
            ),
            'attributes' => array(
                'id' => 'lemon',
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
        return array(
            'drink' => array(
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