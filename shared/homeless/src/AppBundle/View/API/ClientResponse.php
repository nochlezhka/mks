<?php

namespace AppBundle\View\API;

use AppBundle\Entity\Client;
use AppBundle\Entity\ClientFieldValue;

class ClientResponse {

    /**
     * @var int
     */
    private $id;

    /**
     * Название файла с фотографией (хранится с помощью VichUploaderBundle)
     * @var string
     */
    private $photoName;

    /**
     * Дата рождения
     * @var \DateTime
     */
    private $birthDate;

    /**
     * Место рождения
     * @var string
     */
    private $birthPlace;

    /**
     * Пол
     * @var int
     */
    private $gender;

    /**
     * Имя
     * @var string
     */
    private $firstname;

    /**
     * Отчество
     * @var string
     */
    private $middlename;

    /**
     * Фамилия
     * @var string
     */
    private $lastname;

    /**
     * Фамилия
     * @var string
     */
    private $isHomeless = true;


    /**
     * Болезни (массив строк на русском)
     * @var array
     */
    private $diseases;

    public function __construct(Client $client)
    {
        $this->id = $client->getId();
        $this->firstname = $client->getFirstname();
        $this->middlename = $client->getMiddlename();
        $this->lastname = $client->getLastname();
        $this->birthDate = $client->getBirthDate();
        $this->gender = $client->getGender();
        $this->birthPlace = $client->getBirthPlace();
        $this->isHomeless = $client->getisHomeless();
        $this->photoName = $client->getPhotoName();
    }

    /**
     * @param ClientFieldValue|null $diseases
     */
    public function setDiseases($diseases)
    {
        if (is_null($diseases)) {
            return;
        }

        $this->diseases = array_map(function ($d) {
            return $d->getName();
        }, $diseases->getValue()->getValues());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPhotoName()
    {
        return $this->photoName;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @return string
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getIsHomeless()
    {
        return $this->isHomeless;
    }

    /**
     * @return array
     */
    public function getDiseases()
    {
        return $this->diseases;
    }

}
