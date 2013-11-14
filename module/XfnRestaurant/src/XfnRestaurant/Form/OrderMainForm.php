<?php

namespace XfnRestaurant\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use XfnRestaurant\Entity\Order;

class OrderMainForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order-main-form');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'type',
            'options' => array(
                'label' => 'What do you want to order ?',
                'value_options' => array(
                    Order::TYPE_LUNCH => 'Lunch',
                    Order::TYPE_DRINK => 'Drink',
                ),
                'label_attributes' => array(
                    'class' => 'type'
                ),
            )
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
        );
    }
}