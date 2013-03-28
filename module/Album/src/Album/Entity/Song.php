<?php

namespace Album\Entity;

use Doctrine\ORM\Mapping as ORM;
use Loculus\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex as RegexValidator;
use Loculus\Filter\Duration as FixDuration;
use Album\Model\Album as AlbumModel;

/**
 * A music album.
 *
 * @ORM\Entity
 * @ORM\Table(name="songs")
 * @property int $position
 * @property string $name
 * @property time $duration
 * @property int $disc
 * @property int $id
 * @property Album $album
 * @property int $album_id
 */
class Song implements InputFilterAwareInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @ORM\Column(type="string", length=63)
     */
    protected $name;

    /**
     * @ORM\Column(type="time")
     */
    protected $duration;

    /**
     * @ORM\Column(type="integer", options={"default"=1})
     */
    protected $disc;

    /**
     * @ORM\Column(type="integer")
     */
    protected $album_id;

    /**
     * Album instance
     *
     * @var Album\Entity\Album
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="songs")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     * })
     */
    protected $album;

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
        $this->id       = $data['id'];
        $this->position = $data['position'];
        $this->name     = $data['name'];
        $this->duration = new \DateTime($data['duration']);
        $this->disc     = $data['disc'];
        $this->album_id = $data['album_id'];
        $this->album    = isset($data['album']) ? $data['album'] : null;
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
                'name'       => 'album_id',
                'required'   => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array(
                        'name' => 'RegEx',
                        'options' => array(
                            'pattern' => '/^[1-9][0-9]*$/',
                            'messages' => array(
                                RegexValidator::NOT_MATCH => 'Given string seems to not be a valid album id'
                            )
                        ),
                    )
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'       => 'position',
                'required'   => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array(
                        'name' => 'RegEx',
                        'options' => array(
                            'pattern' => '/^[1-9][0-9]*$/',
                            'messages' => array(
                                RegexValidator::NOT_MATCH => 'Given string seems to not be a valid position'
                            )
                        ),
                    )
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
                'name'     => 'duration',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    new FixDuration(),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 5,
                            'max'      => 8,
                        ),
                    ),
                    new RegexValidator('/^\d\d:\d\d(:\d\d)?$/'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'disc',
                'required' => true,
                'filters'  => array(
                    array('name'    => 'Int'),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
