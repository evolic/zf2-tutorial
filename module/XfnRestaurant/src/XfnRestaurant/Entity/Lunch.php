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
 * A lunch
 *
 * @ORM\Entity(repositoryClass="XfnRestaurant\Doctrine\Repository\LunchsRepository")
 * @ORM\Table(name="lunches")
 * @property int $id
 * @property float $price
 * @property Meal $meal
 * @property Dessert $dessert
 */
class Lunch implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Meal instance
     *
     * @var XfnRestaurant\Entity\Meal
     * @ORM\ManyToOne(targetEntity="Meal", inversedBy="lunches")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="meal_id", referencedColumnName="id")
     * })
     */
    protected $meal;

    /**
     * Dessert instance
     *
     * @var XfnRestaurant\Entity\Dessert
     * @ORM\ManyToOne(targetEntity="Dessert", inversedBy="lunches")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="dessert_id", referencedColumnName="id")
     * })
     */
    protected $dessert;

    /**
     * @ORM\ManyToMany(targetEntity="Order", inversedBy="lunches")
     * @ORM\JoinTable(name="lunch2order",
     *  joinColumns={@ORM\JoinColumn(name="lunch_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")}
     * ))
     **/
    private $orders;


    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     *
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
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
        $this->id      = isset($data['id']) ? $data['id'] : null;
        $this->meal    = $data['meal'];
        $this->dessert = $data['dessert'];
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
                'name'       => 'id',
                'required'   => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 63,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'       => 'price',
                'required'   => true,
                'filters' => array(
                    array('name' => 'Float'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Float',
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}