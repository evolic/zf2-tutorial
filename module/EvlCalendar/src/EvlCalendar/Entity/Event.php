<?php

namespace EvlCalendar\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Zend\Validator\InArray,
    Zend\Validator\NotEmpty as NotEmptyValidator,
    Zend\Validator\Regex as RegexValidator;
use Loculus\Entity\Base as BaseEntity,
    Loculus\Filter\StringToBoolean as StringToBooleanFilter;
use Users\Entity\Scientist;

/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 * @property int $id
 * @property string $name
 * @property string $starting_at
 * @property string $ending_at
 * @property boolean $all_day
 */
class Event implements InputFilterAwareInterface
{


    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=63)
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $started_at;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $ended_at;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    protected $all_day;


    public function __construct()
    {
    }


    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
        return $this;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        $this->id          = $data['id'];
        $this->name        = $data['title'];
        $this->started_at  = isset($data['start']) ? new \DateTime($data['start']) : null;
        $this->ended_at    = isset($data['end']) ? new \DateTime($data['end']) : null;
        $this->all_day     = $data['all_day'];
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            // event id
            $inputFilter->add($factory->createInput(array(
                'name'       => 'id',
                'required'   => false,
            )));

            // transaction id, based on JavaScript timestamp related to create datatime
            $inputFilter->add($factory->createInput(array(
                'name'       => 'ts',
                'required'   => false,
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 63,
                        )
                    ),
                )
            )));

            $datetimePattern = '/^\d{4}-\d{2}-\d{2} ([0-1]\d|2[0-3]):[0-5]\d:[0-5]\d$/';

            $inputFilter->add($factory->createInput(array(
                'name'     => 'start',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options' => array(
                            'pattern' => $datetimePattern,
                            'messages' => array(
                                RegexValidator::NOT_MATCH => 'Given string seems to not be a valid datetime'
                            )
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name'     => 'end',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options' => array(
                            'pattern' => $datetimePattern,
                            'messages' => array(
                                RegexValidator::NOT_MATCH => 'Given string seems to not be a valid datetime'
                            )
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'       => 'all_day',
                'required'   => false,
                'filters' => array(
                    new StringToBooleanFilter(),
                ),
                'validators' => array(
                    array(
                        'name' => 'InArray',
                        'options' => array(
                            'haystack' => array(true, false),
                            'messages' => array(
                                InArray::NOT_IN_ARRAY => "'%value%' is not a valid all day status"
                            )
                        )
                    )
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
