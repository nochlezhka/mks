<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Клиент
 * @ORM\Entity()
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
    private ?string $photoName = null;

    /**
     * Дата рождения
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $birthDate = null;

    /**
     * Место рождения
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $birthPlace = null;

    /**
     * Пол
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $gender = null;

    /**
     * Имя
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $firstname = null;

    /**
     * Отчество
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $middlename = null;

    /**
     * Фамилия
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $lastname = null;

    /**
     * Фамилия
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isHomeless = true;

    /**
     * Место последнего проживания
     * @ORM\ManyToOne(targetEntity="District")
     * @ORM\JoinColumn(name="last_residence_district_id", referencedColumnName="id")
     */
    private District $lastResidenceDistrict;

    /**
     * Место последней регистрации
     * @ORM\ManyToOne(targetEntity="District")
     * @ORM\JoinColumn(name="last_registration_district_id", referencedColumnName="id")
     */
    private District $lastRegistrationDistrict;

    /**
     * Значения дополнительных полей
     * @ORM\OneToMany(targetEntity="ClientFieldValue", mappedBy="client", cascade="remove")
     */
    private Collection $fieldValues;

    /**
     * Примечания
     * @ORM\OneToMany(targetEntity="Note", mappedBy="client", cascade="remove")
     * @ORM\OrderBy({"createdAt" = "DESC", "id" = "DESC"})
     */
    private Collection $notes;

    /**
     * Договоры
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="client", cascade="remove")
     * @ORM\OrderBy({"dateFrom" = "DESC"})
     */
    private Collection $contracts;

    /**
     * Документы
     * @ORM\OneToMany(targetEntity="Document", mappedBy="client", cascade="remove")
     */
    private Collection $documents;

    /**
     * Данные о проживаниях в приюте (договоры о заселении)
     * @ORM\OneToMany(targetEntity="ShelterHistory", mappedBy="client", cascade="remove")
     */
    private Collection $shelterHistories;

    /**
     * Загруженные файлы документов
     * @ORM\OneToMany(targetEntity="DocumentFile", mappedBy="client", cascade="remove")
     */
    private Collection $documentFiles;

    /**
     * Полученные услуги
     * @ORM\OneToMany(targetEntity="Service", mappedBy="client", cascade="remove")
     * @ORM\OrderBy({"createdAt" = "DESC", "id" = "DESC"})
     */
    private Collection $services;

    /**
     * Справки
     * @ORM\OneToMany(targetEntity="Certificate", mappedBy="client", cascade="remove")
     */
    private Collection $certificates;

    /**
     * Построенные документы
     * @ORM\OneToMany(targetEntity="GeneratedDocument", mappedBy="client", cascade="remove")
     */
    private Collection $generatedDocuments;

    /**
     * @ORM\OneToMany(targetEntity="ViewedClient", mappedBy="client", cascade="remove")
     */
    private Collection $clientViews;

    /**
     * @ORM\OneToMany(targetEntity="HistoryDownload", mappedBy="client", cascade="remove")
     */
    private Collection $historyDownloads;

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

    public function getPhotoPathWeb(): string
    {
        return 'uploads/images/client/photo/' . substr($this->getPhotoName(), 0, 2) . '/' . $this->getPhotoName();
    }

    public function getPhotoPath(): string
    {
        return __DIR__ . '/../../../web/' . $this->getPhotoPathWeb();
    }

    public function getPhotoSize($width, $height): array
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

    public function getPhotoFileBase64(): ?string
    {
        if (!$this->isImage()) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($this->getPhotoPath()));
    }

    private function isImage(): bool
    {
        return file_exists($this->getPhotoPath()) && @is_array(getimagesize($this->getPhotoPath()));
    }

    public function setPhoto($photo = null): Client
    {
        $this->photo = $photo;

        if ($photo) {
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    /**
     * ФИО
     */
    public function getFullname(): string
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
     * @return ClientFieldOption|DateTime|Collection|null|string
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
    public array $additionalFieldValues = [];

    /**
     * Коды полей, для которых необходимо удалить объекты значений
     * для обработки в методах prePersist и preUpdate админки AppBundle\Admin\ClientAdmin
     * @var array
     */
    public array $additionalFieldValuesToRemove = [];

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
    public function hasRegistrationDocument(): bool
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
    public function getInitials(): string
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
    public function getLastnameAndInitials(): string
    {
        if (empty($this->lastname)) {
            return $this->firstname;
        }

        if (empty($this->firstname)) {
            return $this->lastname;
        }

        return $this->lastname . ' ' . $this->getInitials();
    }

    /**
     * Set photoName
     *
     * @param string|null $photoName
     *
     * @return Client
     */
    public function setPhotoName(?string $photoName): Client
    {
        $this->photoName = $photoName;

        return $this;
    }

    /**
     * Get photoName
     *
     * @return string
     */
    public function getPhotoName(): ?string
    {
        return $this->photoName;
    }

    /**
     * Set birthDate
     *
     * @param DateTime|null $birthDate
     *
     * @return Client
     */
    public function setBirthDate(?DateTime $birthDate): Client
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return DateTime
     */
    public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }

    /**
     * Set birthPlace
     *
     * @param string|null $birthPlace
     *
     * @return Client
     */
    public function setBirthPlace(?string $birthPlace): Client
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    /**
     * Get birthPlace
     *
     * @return string
     */
    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    /**
     * Set gender
     *
     * @param int|null $gender
     *
     * @return Client
     */
    public function setGender(?int $gender): Client
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer
     */
    public function getGender(): ?int
    {
        return $this->gender;
    }

    /**
     * Set firstname
     *
     * @param string|null $firstname
     *
     * @return Client
     */
    public function setFirstname(?string $firstname): Client
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set middlename
     *
     * @param string|null $middlename
     *
     * @return Client
     */
    public function setMiddlename(?string $middlename): Client
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    /**
     * Set lastname
     *
     * @param string|null $lastname
     *
     * @return Client
     */
    public function setLastname(?string $lastname): Client
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set lastResidenceDistrict
     *
     * @param District|null $lastResidenceDistrict
     *
     * @return Client
     */
    public function setLastResidenceDistrict(District $lastResidenceDistrict = null): Client
    {
        $this->lastResidenceDistrict = $lastResidenceDistrict;

        return $this;
    }

    /**
     * Get lastResidenceDistrict
     *
     * @return District
     */
    public function getLastResidenceDistrict(): District
    {
        return $this->lastResidenceDistrict;
    }

    /**
     * Set lastRegistrationDistrict
     *
     * @param District|null $lastRegistrationDistrict
     *
     * @return Client
     */
    public function setLastRegistrationDistrict(District $lastRegistrationDistrict = null): Client
    {
        $this->lastRegistrationDistrict = $lastRegistrationDistrict;

        return $this;
    }

    /**
     * Get lastRegistrationDistrict
     *
     * @return District
     */
    public function getLastRegistrationDistrict(): District
    {
        return $this->lastRegistrationDistrict;
    }

    /**
     * Add fieldValue
     *
     * @param ClientFieldValue $fieldValue
     *
     * @return Client
     */
    public function addFieldValue(ClientFieldValue $fieldValue): Client
    {
        $this->fieldValues[] = $fieldValue;

        return $this;
    }

    /**
     * Remove fieldValue
     *
     * @param ClientFieldValue $fieldValue
     */
    public function removeFieldValue(ClientFieldValue $fieldValue)
    {
        $this->fieldValues->removeElement($fieldValue);
    }

    /**
     * Get fieldValues
     *
     * @return Collection
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * Add note
     *
     * @param Note $note
     *
     * @return Client
     */
    public function addNote(Note $note): Client
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param Note $note
     */
    public function removeNote(Note $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add contract
     *
     * @param Contract $contract
     *
     * @return Client
     */
    public function addContract(Contract $contract): Client
    {
        $this->contracts[] = $contract;

        return $this;
    }

    /**
     * Remove contract
     *
     * @param Contract $contract
     */
    public function removeContract(Contract $contract)
    {
        $this->contracts->removeElement($contract);
    }

    /**
     * Get contracts
     *
     * @return Collection
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    /**
     * Add document
     *
     * @param Document $document
     *
     * @return Client
     */
    public function addDocument(Document $document): Client
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param Document $document
     */
    public function removeDocument(Document $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add shelterHistory
     *
     * @param ShelterHistory $shelterHistory
     *
     * @return Client
     */
    public function addShelterHistory(ShelterHistory $shelterHistory): Client
    {
        $this->shelterHistories[] = $shelterHistory;

        return $this;
    }

    /**
     * Remove shelterHistory
     *
     * @param ShelterHistory $shelterHistory
     */
    public function removeShelterHistory(ShelterHistory $shelterHistory)
    {
        $this->shelterHistories->removeElement($shelterHistory);
    }

    /**
     * Get shelterHistories
     *
     * @return Collection
     */
    public function getShelterHistories()
    {
        return $this->shelterHistories;
    }

    /**
     * Add documentFile
     *
     * @param DocumentFile $documentFile
     *
     * @return Client
     */
    public function addDocumentFile(DocumentFile $documentFile): Client
    {
        $this->documentFiles[] = $documentFile;

        return $this;
    }

    /**
     * Remove documentFile
     *
     * @param DocumentFile $documentFile
     */
    public function removeDocumentFile(DocumentFile $documentFile)
    {
        $this->documentFiles->removeElement($documentFile);
    }

    /**
     * Get documentFiles
     *
     * @return Collection
     */
    public function getDocumentFiles()
    {
        return $this->documentFiles;
    }

    /**
     * Add service
     *
     * @param Service $service
     *
     * @return Client
     */
    public function addService(Service $service): Client
    {
        $this->services[] = $service;

        return $this;
    }

    /**
     * Remove service
     *
     * @param Service $service
     */
    public function removeService(Service $service)
    {
        $this->services->removeElement($service);
    }

    /**
     * Get services
     *
     * @return Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Add certificate
     *
     * @param Certificate $certificate
     *
     * @return Client
     */
    public function addCertificate(Certificate $certificate): Client
    {
        $this->certificates[] = $certificate;

        return $this;
    }

    /**
     * Remove certificate
     *
     * @param Certificate $certificate
     */
    public function removeCertificate(Certificate $certificate)
    {
        $this->certificates->removeElement($certificate);
    }

    /**
     * Get certificates
     *
     * @return Collection
     */
    public function getCertificates(): Collection
    {
        return $this->certificates;
    }

    /**
     * Add generatedDocument
     *
     * @param GeneratedDocument $generatedDocument
     *
     * @return Client
     */
    public function addGeneratedDocument(GeneratedDocument $generatedDocument): Client
    {
        $this->generatedDocuments[] = $generatedDocument;

        return $this;
    }

    /**
     * Remove generatedDocument
     *
     * @param GeneratedDocument $generatedDocument
     */
    public function removeGeneratedDocument(GeneratedDocument $generatedDocument)
    {
        $this->generatedDocuments->removeElement($generatedDocument);
    }

    /**
     * Get generatedDocuments
     *
     * @return Collection
     */
    public function getGeneratedDocuments()
    {
        return $this->generatedDocuments;
    }

    /**
     * Get isHomeless
     */
    public function getisHomeless(): ?bool
    {
        return $this->isHomeless;
    }

    /**
     * Set isHomeless
     *
     * @param bool|null $isHomeless
     * @return Client
     */
    public function setIsHomeless(?bool $isHomeless): Client
    {
        $this->isHomeless = $isHomeless;

        return $this;
    }

    /**
     * Get NotIsHomeless
     *
     */
    public function getNotIsHomeless(): ?bool
    {
        return !$this->isHomeless;
    }

    /**
     * Set notIsHomeless
     *
     * @param bool|null $notIsHomeless
     * @return Client
     */
    public function setNotIsHomeless(?bool $notIsHomeless): Client
    {
        $this->isHomeless = !$notIsHomeless;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientViews()
    {
        return $this->clientViews;
    }

    /**
     * @param mixed $clientViews
     */
    public function setClientViews($clientViews): void
    {
        $this->clientViews = $clientViews;
    }

    /**
     * @return Collection
     */
    public function getHistoryDownloads(): Collection
    {
        return $this->historyDownloads;
    }

    /**
     * @param Collection $historyDownloads
     */
    public function setHistoryDownloads(Collection $historyDownloads): void
    {
        $this->historyDownloads = $historyDownloads;
    }
}
