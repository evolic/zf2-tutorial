<?php

namespace XfnRestaurant\Entity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * A restaurant's order
 *
 * @ORM\Entity(repositoryClass="XfnRestaurant\Doctrine\Repository\OrdersRepository")
 * @ORM\Table(name="orders")
 * @property int $id
 * @property float $price
 * @property DateTime $created_at
 */
class Order implements InputFilterAwareInterface
{
    const TYPE_LUNCH = 'lunch';
    const TYPE_DRINK = 'drink';


    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="float")
     */
    protected $price;

    /**
     * Date of creation
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;


    /**
     * Inverse Side
     *
     * @ORM\ManyToMany(targetEntity="XfnRestaurant\Entity\Lunch", mappedBy="orders")
     */
    private $lunches;

    /**
     * Meals from specified cuisine
     *
     * @var Doctrine\Common\Collections\ArrayCollection $drinks2order
     * @ORM\OneToMany(targetEntity="Drink2Order", mappedBy="order", cascade={"persist","remove"})
     */
    protected $drinks2order;


    public function __construct()
    {
        $this->lunches      = new ArrayCollection();
        $this->drinks2order = new ArrayCollection();
    }

    /**
     *
     * @return ArrayCollection
     */
    public function getLunches()
    {
        return $this->lunches;
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
        $this->id    = isset($data['id']) ? $data['id'] : null;
        $this->price = (float) $data['price'];
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