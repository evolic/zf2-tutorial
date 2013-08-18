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
use Loculus\Entity\Base as BaseEntity;
use Users\Entity\Scientist;

/**
 * @ORM\Entity
 * @ORM\Table(name="holidays")
 * @property int $id
 * @property string $name
 * @property string $dated_at
 * @property int $year
 * @property int $weekday
 * @property int $type
 * @property bool $constant
 */
class Holiday implements InputFilterAwareInterface
{
    const TYPE_NATIONAL = 1;
    const TYPE_ADDITIONAL = 2;

    const DAY_MONDAY    = 1; // 0-1, 4-23
    const DAY_TUESDAY   = 2;
    const DAY_WEDNESDAY = 4;
    const DAY_THURSDAY  = 8;
    const DAY_FRIDAY    = 16;
    const DAY_SATURDAY  = 32;
    const DAY_SUNDAY    = 64;

    const HOLIDAY_NAME_NEW_YEAR           = "Nowy Rok"; // 01-01
    const HOLIDAY_NAME_EPIPHANY           = "Trzech Króli"; // 01-06
    const HOLIDAY_NAME_EASTER_SUNDAY      = "Wielkanoc, Wielka Niedziela"; // ruchome, marzec-kwiecień, niedziela
    const HOLIDAY_NAME_EASTER_MONDAY      = "Wielkanoc, Poniedziałek wielkanocny"; // ruchome, poniedziałek, 1 dzień po Wielkiej Nocy
    const HOLIDAY_NAME_FIRST_OF_MAY       = "Święto pracy"; // 05-01
    const HOLIDAY_NAME_THIRD_OF_MAY       = "Trzeciego maja"; // 05-03
    const HOLIDAY_NAME_PENTECOST          = "Niedziela Zesłania Ducha św. (Zielone Świątki)"; // ruchome, niedziela, 7 niedziela po Wielkiej Nocy
    const HOLIDAY_NAME_CORPUS_CHRISTI     = "Boże Ciało"; // ruchome, czwartek, w 60 dni po Wielkiej Nocy
    const HOLIDAY_NAME_ASSUMPTION_OF_MARY = "Wniebowzięcie Najświętszej Maryi Panny"; // 08-15
    const HOLIDAY_NAME_ALL_SAINTS         = "Wszystkich Świętych"; // 11-01
    const HOLIDAY_NAME_INDEPANDENCE_DAY   = "Święto Niepodległości"; // 11-11
    const HOLIDAY_NAME_CHRISTMAS          = "Boże Narodzenie"; // 12-25
    const HOLIDAY_NAME_SAINT_STEPHEN      = "Boże Narodzenie, św. Szczepana"; // 12-26

    public static $holidays = array(
        array(
            'name' => self::HOLIDAY_NAME_NEW_YEAR,
            'date' => "-01-01",
            'constant' => true,
        ),
        array(
            'name' => self::HOLIDAY_NAME_EPIPHANY,
            'date' => "-01-06",
            'constant' => true,
        ),
        array(
            'name' => self::HOLIDAY_NAME_EASTER_SUNDAY,
            'date' => null,
            'constant' => false,
            'weekday' => self::DAY_SUNDAY,
        ),
        array(
            'name' => self::HOLIDAY_NAME_EASTER_MONDAY,
            'date' => null,
            'constant' => false,
            'weekday' => self::DAY_MONDAY,
        ),
        array(
            'name' => self::HOLIDAY_NAME_FIRST_OF_MAY,
            'date' => '-05-01',
            'constant' => true,
        ),
        array(
            'name' => self::HOLIDAY_NAME_THIRD_OF_MAY,
            'date' => '-05-03',
            'constant' => true,
        ),
        array(
            'name' => self::HOLIDAY_NAME_PENTECOST,
            'date' => null,
            'constant' => false,
            'weekday' => self::DAY_SUNDAY,
        ),
        array(
            'name' => self::HOLIDAY_NAME_CORPUS_CHRISTI,
            'date' => null,
            'constant' => false,
            'weekday' => self::DAY_THURSDAY,
        ),
        array(
            'name' => self::HOLIDAY_NAME_ASSUMPTION_OF_MARY,
            'date' => '-08-15',
            'constant' => true,
        ),
        array(
            'name' => self::HOLIDAY_NAME_ALL_SAINTS,
            'date' => '-11-01',
            'constant' => true,
        ),
        array(
            'name' => self::HOLIDAY_NAME_INDEPANDENCE_DAY,
            'date' => '-11-11',
            'constant' => true,
        ),
        array(
            'name' => self::HOLIDAY_NAME_CHRISTMAS,
            'date' => '-12-25',
            'constant' => true,
        ),
        array(
            'name' => self::HOLIDAY_NAME_SAINT_STEPHEN,
            'date' => '-12-26',
            'constant' => true,
        ),
    );


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
     * @ORM\Column(type="date")
     * @var \DateTime
     */
    protected $dated_at;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $year;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $weekday;

    /**
     * @ORM\Column(type="smallint", options={"default"=1})
     */
    protected $type;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $constant;



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
        $this->name        = $data['name'];
        $this->dated_at    = isset($data['dated_at']) ? new \DateTime($data['dated_at']) : null;
        $this->year        = $data['year'];
        $this->weekday     = $data['weekday'];
        $this->type        = $data['type'];
        $this->constant    = (bool) $data['constant'];
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
                    array('name'    => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'description',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'starting_at',
//                 'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options' => array(
                            'pattern' => '/^\d{4}-\d{2}-\d{2}$/',
                            'messages' => array(
                                RegexValidator::NOT_MATCH => 'Given string seems to not be a valid date'
                            )
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name'     => 'ending_at',
//                 'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Regex',
                        'options' => array(
                            'pattern' => '/^\d{4}-\d{2}-\d{2}$/',
                            'messages' => array(
                                RegexValidator::NOT_MATCH => 'Given string seems to not be a valid date'
                            )
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'       => 'status',
                'required'   => true,
                'filters' => array(
//                     array('name'    => 'Int'),
                ),
                'validators' => array(
                    array(
                        'name' => 'InArray',
                        'options' => array(
                            'haystack' => BaseEntity::$statuses,
                            'messages' => array(
                                InArray::NOT_IN_ARRAY => "'%value%' is not a valid status"
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
