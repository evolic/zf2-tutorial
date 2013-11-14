<?php

namespace XfnRestaurant\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * A table mapping relationship between drinks and orders
 *
 * @ORM\Entity
 * @ORM\Table(name="drink2order")
 * @property int $drink_id
 * @property int $order_id
 * @property boolean $ice_cubes
 * @property boolean $lemon
 * @property Drink $drink
 * @property Order $order
 */
class Drink2Order implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $drink_id;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $order_id;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    protected $lemon;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    protected $ice_cubes;


    /**
     * Drink instance
     *
     * @var XfnRestaurant\Entity\Drink
     * @ORM\ManyToOne(targetEntity="Drink", inversedBy="orders2drink")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="drink_id", referencedColumnName="id")
     * })
     */
    protected $drink;

    /**
     * Drink instance
     *
     * @var XfnRestaurant\Entity\Order
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="drinks2order")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * })
     */
    protected $order;


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

            $inputFilter->add($factory->createInput(array(
                'name'       => 'drink_id',
                'required'   => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'       => 'order_id',
                'required'   => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}