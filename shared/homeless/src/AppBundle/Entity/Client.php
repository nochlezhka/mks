<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Клиент
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 * @Vich\Uploadable
 */
class Client extends BaseEntity
{
    /**
     * Мужской пол
     */
    const GENDER_MALE = 1;

    /**
     * Женский пол
     */
    const GENDER_FEMALE = 2;

    /**
     * Название файла с фотографией (хранится с помощью VichUploaderBundle)
     * @ORM\Column(type="string", nullable=true)
     */
    private $photoName;

    /**
     * Дата рождения
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDate;

    /**
     * Место рождения
     * @ORM\Column(type="string", nullable=true)
     */
    private $birthPlace;

    /**
     * Пол
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gender;

    /**
     * Имя
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstname;

    /**
     * Отчество
     * @ORM\Column(type="string", nullable=true)
     */
    private $middlename;

    /**
     * Фамилия
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastname;

    /**
     * Фамилия
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isHomeless = true;

    /**
     * Место последнего проживания
     * @ORM\ManyToOne(targetEntity="District")
     * @ORM\JoinColumn(name="last_residence_district_id", referencedColumnName="id")
     */
    private $lastResidenceDistrict;

    /**
     * Место последней регистрации
     * @ORM\ManyToOne(targetEntity="District")
     * @ORM\JoinColumn(name="last_registration_district_id", referencedColumnName="id")
     */
    private $lastRegistrationDistrict;

    /**
     * Значения дополнительных полей
     * @ORM\OneToMany(targetEntity="ClientFieldValue", mappedBy="client", cascade={"remove"})
    //  * @ORM\OneToMany(targetEntity="ClientFieldValue", mappedBy="client")
     */
    private $fieldValues;

    /**
     * Примечания
     * @ORM\OneToMany(targetEntity="Note", mappedBy="client", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC", "id" = "DESC"})
     */
    private $notes;

    /**
     * Договоры
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="client", cascade={"remove"})
    //  * @ORM\OneToMany(targetEntity="Contract", mappedBy="client")
     * @ORM\OrderBy({"dateFrom" = "DESC"})
     */
    private $contracts;

    /**
     * Документы
     * @ORM\OneToMany(targetEntity="Document", mappedBy="client", cascade={"remove"})
    //  * @ORM\OneToMany(targetEntity="Document", mappedBy="client")
     */
    private $documents;

    /**
     * Данные о проживаниях в приюте (договоры о заселении)
     * @ORM\OneToMany(targetEntity="ShelterHistory", mappedBy="client", cascade={"remove"})
    //  * @ORM\OneToMany(targetEntity="ShelterHistory", mappedBy="client")
     */
    private $shelterHistories;

    /**
     * Загруженные файлы документов
     * @ORM\OneToMany(targetEntity="DocumentFile", mappedBy="client", cascade={"remove"})
    //  * @ORM\OneToMany(targetEntity="DocumentFile", mappedBy="client")
     */
    private $documentFiles;

    /**
     * Полученные услуги
     * @ORM\OneToMany(targetEntity="Service", mappedBy="client", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC", "id" = "DESC"})
     */
    private $services;

    /**
     * Выданные вещи (одежда, предметы гигиены, ...)
     * @ORM\OneToMany(targetEntity="Delivery", mappedBy="client")
     * @ORM\OrderBy({"createdAt" = "DESC", "id" = "DESC"})
     */
    private $deliveries;

    /**
     * Справки
     * @ORM\OneToMany(targetEntity="Certificate", mappedBy="client", cascade={"remove"})
    //  * @ORM\OneToMany(targetEntity="Certificate", mappedBy="client")
     */
    private $certificates;

    /**
     * Построенные документы
     * @ORM\OneToMany(targetEntity="GeneratedDocument", mappedBy="client", cascade={"remove"})
    //  * @ORM\OneToMany(targetEntity="GeneratedDocument", mappedBy="client")
     */
    private $generatedDocuments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fieldValues = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->shelterHistories = new ArrayCollection();
        $this->documentFiles = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->generatedDocuments = new ArrayCollection();
    }

    /**
     * @Vich\UploadableField(mapping="client_photo", fileNameProperty="photoName")
     */
    private $photo;

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getPhotoPathWeb()
    {
        return 'uploads/images/client/photo/' . substr($this->getPhotoName(), 0, 2) . '/' . $this->getPhotoName();
    }

    public function getPhotoPath()
    {
        return __DIR__ . '/../../../web/' . $this->getPhotoPathWeb();
    }

    public function getPhotoSize($width, $height)
    {
        if (!$this->isImage()) {
            return [$width, $height];
        }

        list($width_orig, $height_orig) = getimagesize($this->getPhotoPath());
        if ($width < $width_orig || $height < $height_orig) {
            if ($width_orig > $height_orig) {
                $height = $height_orig / ($width_orig / $width);
            } else {
                $width = $width_orig / ($height_orig / $height);
            }
        } else {
            $width = $width_orig;
            $height = $height_orig;
        }

        return [$width, $height];
    }

    public function getPhotoFileBase64()
    {
        if (!$this->isImage()) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($this->getPhotoPath()));
    }

    private function isImage()
    {
        return file_exists($this->getPhotoPath()) && @is_array(getimagesize($this->getPhotoPath()));
    }

    public function setPhoto($photo = null)
    {
        $this->photo = $photo;

        if ($photo) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    /**
     * ФИО
     */
    public function getFullname()
    {
        $fullname = [];

        $lastname = $this->getLastname();
        if (!empty($lastname)) {
            $fullname[] = $lastname;
        }

        $firstname = $this->getFirstname();
        if (!empty($firstname)) {
            $fullname[] = $firstname;
        }

        $middlename = $this->getMiddlename();
        if (!empty($middlename)) {
            $fullname[] = $middlename;
        }

        return implode(' ', $fullname);
    }

    public function __toString()
    {
        return $this->getFullname();
    }

    /**
     * Последний договор
     */
    public function getLastContract()
    {
        return $this->contracts->first();
    }

    public function __get($name)
    {
        if (substr($name, 0, 15) === 'additionalField') {
            return $this->$name();
        }
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 15) === 'additionalField') {
            return $this->getAdditionalFieldValue(substr($name, 15));
        }
    }

    public function __set($name, $value)
    {
        if (substr($name, 0, 15) === 'additionalField') {
            $this->setAdditionalFieldValue(substr($name, 15), $value);
        }
    }

    /**
     * Возвращает объект значения дополнительного поля клиента по коду поля
     * @param $fieldCode
     * @return mixed|null
     */
    public function getAdditionalFieldValueObject($fieldCode)
    {
        foreach ($this->fieldValues as $fieldValue) {
            $field = $fieldValue->getField();

            if ($field->getCode() == $fieldCode) {
                return $fieldValue;
            }
        }

        return null;
    }

    /**
     * Возвращает значение дополнительного поля клиента по коду поля
     * @param $fieldCode
     * @return ClientFieldOption|\DateTime|\Doctrine\Common\Collections\Collection|null|string
     */
    public function getAdditionalFieldValue($fieldCode)
    {
        $fieldValue = $this->getAdditionalFieldValueObject($fieldCode);

        if ($fieldValue instanceof ClientFieldValue) {
            return $fieldValue->getValue();
        }

        return null;
    }

    /**
     * Значения дополнительных полей клиента, установленных при сохранении
     * для обработки в методах prePersist и preUpdate админки AppBundle\Admin\ClientAdmin
     * @var array
     */
    public $additionalFieldValues = [];

    /**
     * Коды полей, для которых необходимо удалить объекты значений
     * для обработки в методах prePersist и preUpdate админки AppBundle\Admin\ClientAdmin
     * @var array
     */
    public $additionalFieldValuesToRemove = [];

    /**
     * Подготавливает массивы $additionalFieldValues и $additionalFieldValuesToRemove
     * для обработки в методах prePersist и preUpdate админки AppBundle\Admin\ClientAdmin
     * @param $fieldCode
     * @param $value
     */
    public function setAdditionalFieldValue($fieldCode, $value)
    {
        if (empty($value) || ($value instanceof Collection && $value->isEmpty())) {
            $this->additionalFieldValuesToRemove[] = $fieldCode;
            return;
        }

        $this->additionalFieldValues[$fieldCode] = $value;
    }

    /**
     * Есть ли у клиента документ для постановки на учет
     * @return bool
     */
    public function hasRegistrationDocument()
    {
        foreach ($this->getDocuments() as $document) {
            $type = $document->getType();

            if (!$type instanceof DocumentType) {
                continue;
            }

            if ($type->getType() == DocumentType::TYPE_REGISTRATION) {
                return true;
            }

        }

        return false;
    }

    /**
     * Иницииалы
     */
    public function getInitials()
    {
        $initials = '';

        if (!empty($this->firstname)) {
            $initials = $initials . mb_substr($this->firstname, 0, 1) . '.';
        }

        if (!empty($this->middlename)) {
            $initials = $initials . mb_substr($this->middlename, 0, 1) . '.';
        }

        return $initials;
    }

    /**
     * Фамилия и инициалы
     * @return string
     */
    public function getLastnameAndInitials()
    {
        if (empty($this->lastname)) {
            return (string)$this->firstname;
        }

        if (empty($this->firstname)) {
            return (string)$this->lastname;
        }

        return $this->lastname . ' ' . $this->getInitials();
    }

    /**
     * Set photoName
     *
     * @param string $photoName
     *
     * @return Client
     */
    public function setPhotoName($photoName)
    {
        $this->photoName = $photoName;

        return $this;
    }

    /**
     * Get photoName
     *
     * @return string
     */
    public function getPhotoName()
    {
        return $this->photoName;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Client
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set birthPlace
     *
     * @param string $birthPlace
     *
     * @return Client
     */
    public function setBirthPlace($birthPlace)
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    /**
     * Get birthPlace
     *
     * @return string
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     *
     * @return Client
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Client
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     *
     * @return Client
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Client
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set lastResidenceDistrict
     *
     * @param \AppBundle\Entity\District $lastResidenceDistrict
     *
     * @return Client
     */
    public function setLastResidenceDistrict(District $lastResidenceDistrict = null)
    {
        $this->lastResidenceDistrict = $lastResidenceDistrict;

        return $this;
    }

    /**
     * Get lastResidenceDistrict
     *
     * @return \AppBundle\Entity\District
     */
    public function getLastResidenceDistrict()
    {
        return $this->lastResidenceDistrict;
    }

    /**
     * Set lastRegistrationDistrict
     *
     * @param \AppBundle\Entity\District $lastRegistrationDistrict
     *
     * @return Client
     */
    public function setLastRegistrationDistrict(District $lastRegistrationDistrict = null)
    {
        $this->lastRegistrationDistrict = $lastRegistrationDistrict;

        return $this;
    }

    /**
     * Get lastRegistrationDistrict
     *
     * @return \AppBundle\Entity\District
     */
    public function getLastRegistrationDistrict()
    {
        return $this->lastRegistrationDistrict;
    }

    /**
     * Add fieldValue
     *
     * @param \AppBundle\Entity\ClientFieldValue $fieldValue
     *
     * @return Client
     */
    public function addFieldValue(ClientFieldValue $fieldValue)
    {
        $this->fieldValues[] = $fieldValue;

        return $this;
    }

    /**
     * Remove fieldValue
     *
     * @param \AppBundle\Entity\ClientFieldValue $fieldValue
     */
    public function removeFieldValue(ClientFieldValue $fieldValue)
    {
        $this->fieldValues->removeElement($fieldValue);
    }

    /**
     * Get fieldValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * Add note
     *
     * @param \AppBundle\Entity\Note $note
     *
     * @return Client
     */
    public function addNote(Note $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \AppBundle\Entity\Note $note
     */
    public function removeNote(Note $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add contract
     *
     * @param \AppBundle\Entity\Contract $contract
     *
     * @return Client
     */
    public function addContract(Contract $contract)
    {
        $this->contracts[] = $contract;

        return $this;
    }

    /**
     * Remove contract
     *
     * @param \AppBundle\Entity\Contract $contract
     */
    public function removeContract(Contract $contract)
    {
        $this->contracts->removeElement($contract);
    }

    /**
     * Get contracts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    /**
     * Add document
     *
     * @param \AppBundle\Entity\Document $document
     *
     * @return Client
     */
    public function addDocument(Document $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \AppBundle\Entity\Document $document
     */
    public function removeDocument(Document $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add shelterHistory
     *
     * @param \AppBundle\Entity\ShelterHistory $shelterHistory
     *
     * @return Client
     */
    public function addShelterHistory(ShelterHistory $shelterHistory)
    {
        $this->shelterHistories[] = $shelterHistory;

        return $this;
    }

    /**
     * Remove shelterHistory
     *
     * @param \AppBundle\Entity\ShelterHistory $shelterHistory
     */
    public function removeShelterHistory(ShelterHistory $shelterHistory)
    {
        $this->shelterHistories->removeElement($shelterHistory);
    }

    /**
     * Get shelterHistories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShelterHistories()
    {
        return $this->shelterHistories;
    }

    /**
     * Add documentFile
     *
     * @param \AppBundle\Entity\DocumentFile $documentFile
     *
     * @return Client
     */
    public function addDocumentFile(DocumentFile $documentFile)
    {
        $this->documentFiles[] = $documentFile;

        return $this;
    }

    /**
     * Remove documentFile
     *
     * @param \AppBundle\Entity\DocumentFile $documentFile
     */
    public function removeDocumentFile(DocumentFile $documentFile)
    {
        $this->documentFiles->removeElement($documentFile);
    }

    /**
     * Get documentFiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocumentFiles()
    {
        return $this->documentFiles;
    }

    /**
     * Add service
     *
     * @param \AppBundle\Entity\Service $service
     *
     * @return Client
     */
    public function addService(Service $service)
    {
        $this->services[] = $service;

        return $this;
    }

    /**
     * Remove service
     *
     * @param \AppBundle\Entity\Service $service
     */
    public function removeService(Service $service)
    {
        $this->services->removeElement($service);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @return mixed
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * @param mixed $deliveries
     * @return Client
     */
    public function setDeliveries($deliveries)
    {
        $this->deliveries = $deliveries;
        return $this;
    }

    /**
     * Add certificate
     *
     * @param \AppBundle\Entity\Certificate $certificate
     *
     * @return Client
     */
    public function addCertificate(Certificate $certificate)
    {
        $this->certificates[] = $certificate;

        return $this;
    }

    /**
     * Remove certificate
     *
     * @param \AppBundle\Entity\Certificate $certificate
     */
    public function removeCertificate(Certificate $certificate)
    {
        $this->certificates->removeElement($certificate);
    }

    /**
     * Get certificates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCertificates()
    {
        return $this->certificates;
    }

    /**
     * Add generatedDocument
     *
     * @param \AppBundle\Entity\GeneratedDocument $generatedDocument
     *
     * @return Client
     */
    public function addGeneratedDocument(GeneratedDocument $generatedDocument)
    {
        $this->generatedDocuments[] = $generatedDocument;

        return $this;
    }

    /**
     * Remove generatedDocument
     *
     * @param \AppBundle\Entity\GeneratedDocument $generatedDocument
     */
    public function removeGeneratedDocument(GeneratedDocument $generatedDocument)
    {
        $this->generatedDocuments->removeElement($generatedDocument);
    }

    /**
     * Get generatedDocuments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGeneratedDocuments()
    {
        return $this->generatedDocuments;
    }

    /**
     * Get isHomeless
     *
     * @return mixed
     */
    public function getisHomeless()
    {
        return $this->isHomeless;
    }

    /**
     * Set isHomeless
     *
     * @param mixed $isHomeless
     *
     * @return Client
     */
    public function setIsHomeless($isHomeless)
    {
        $this->isHomeless = $isHomeless;

        return $this;
    }

    /**
     * Get NotIsHomeless
     *
     * @return mixed
     */
    public function getNotIsHomeless()
    {
        return !$this->isHomeless;
    }

    /**
     * Set notIsHomeless
     *
     * @param mixed $notIsHomeless
     *
     * @return Client
     */
    public function setNotIsHomeless($notIsHomeless)
    {
        $this->isHomeless = !$notIsHomeless;

        return $this;
    }
}
