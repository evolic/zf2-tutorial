<?php

namespace EvlCalendar\Form;

use Zend\Form\Form,
    Zend\Form\FormInterface;
use EvlCalendar\Entity\Event;

class EventForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('event');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'ts',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array(
            'name' => 'start',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Starting at',
            ),
        ));
        $this->add(array(
            'name' => 'end',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Ending at',
            ),
        ));
        $this->add(array(
            'name' => 'all_day',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'All day',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }

    /**
     * Bind an object to the form
     *
     * Ensures the object is populated with validated values.
     *
     * @param  object $object
     * @param  int $flags
     * @return mixed|void
     * @throws Exception\InvalidArgumentException
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        $firephp = \FirePHP::getInstance(true);
        $firephp->info(__METHOD__);

        $object->title   = $object->name;
        $object->start   = $object->started_at->format('Y-m-d H:i:s');
        $object->end     = $object->ended_at->format('Y-m-d H:i:s');

        return parent::bind($object, $flags);
    }
}