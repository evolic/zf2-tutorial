<?php

namespace Album\Form;

use Zend\Form\Form,
    Zend\Form\FormInterface;

class SongForm extends Form
{
    public function __construct($name = null, $discs = 1)
    {
        // we want to ignore the name passed
        parent::__construct('song');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'album_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'position',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Position',
            ),
        ));
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
        $this->add(array(
            'name' => 'duration',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Time'
            )
        ));

        $value_options = array();
        for ($i=1; $i<=$discs; $i++) {
            $value_options[$i] = $i;
        }
        $this->add(array(
            'type'  => 'Zend\Form\Element\Select',
            'name' => 'disc',
            'options' => array(
                'label' => 'Disc',
            ),
            'attributes' => array(
                'options' => $value_options,
                'value' => 1
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
        $object->duration = $object->duration->format('i:s');

        return parent::bind($object, $flags);
    }
}