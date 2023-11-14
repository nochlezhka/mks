<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Клиент
 */
#[ORM\Entity]
#[Vich\Uploadable]
class Client extends BaseEntity
{
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;

    /**
     * Значения дополнительных полей клиента, установленных при сохранении
     * для обработки в методах prePersist и preUpdate админки App\Admin\ClientAdmin
     */
    public array $additionalFieldValues = [];

    /**
     * Коды полей, для которых необходимо удалить объекты значений
     * для обработки в методах prePersist и preUpdate админки App\Admin\ClientAdmin
     *
     * @var array<string>
     */
    public array $additionalFieldValuesToRemove = [];

    /**
     * Название файла с фотографией (хранится с помощью VichUploaderBundle)
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $photoName = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $birthDate = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $birthPlace = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $gender = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $middlename = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isHomeless = true;

    /**
     * Место последнего проживания
     */
    #[ORM\ManyToOne(targetEntity: District::class)]
    #[ORM\JoinColumn(name: 'last_residence_district_id', referencedColumnName: 'id')]
    private ?District $lastResidenceDistrict = null;

    /**
     * Место последней регистрации
     */
    #[ORM\ManyToOne(targetEntity: District::class)]
    #[ORM\JoinColumn(name: 'last_registration_district_id', referencedColumnName: 'id')]
    private ?District $lastRegistrationDistrict = null;

    /**
     * Значения дополнительных полей
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ClientFieldValue::class, cascade: ['remove'])]
    private Collection $fieldValues;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Note::class, cascade: ['remove'])]
    #[ORM\OrderBy(['createdAt' => 'DESC', 'id' => 'DESC'])]
    private Collection $notes;

    /**
     * Договоры
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Contract::class, cascade: ['remove'])]
    #[ORM\OrderBy(['dateFrom' => 'DESC'])]
    private Collection $contracts;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Document::class, cascade: ['remove'])]
    private Collection $documents;

    /**
     * Данные о проживаниях в приюте (договоры о заселении)
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ShelterHistory::class, cascade: ['remove'])]
    private Collection $shelterHistories;

    /**
     * Загруженные файлы документов
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: DocumentFile::class, cascade: ['remove'])]
    private Collection $documentFiles;

    /**
     * Полученные услуги
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Service::class, cascade: ['remove'])]
    #[ORM\OrderBy(['createdAt' => 'DESC', 'id' => 'DESC'])]
    private Collection $services;

    /**
     * Справки
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Certificate::class, cascade: ['remove'])]
    private Collection $certificates;

    /**
     * Построенные документы
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: GeneratedDocument::class, cascade: ['remove'])]
    private Collection $generatedDocuments;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ViewedClient::class, cascade: ['remove'])]
    private Collection $clientViews;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: HistoryDownload::class, cascade: ['remove'])]
    private Collection $historyDownloads;

    #[Vich\UploadableField(mapping: 'client_photo', fileNameProperty: 'photoName')]
    private ?File $photo = null;

    public function __construct()
    {
        $this->fieldValues = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->shelterHistories = new ArrayCollection();
        $this->documentFiles = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->certificates = new ArrayCollection();
        $this->generatedDocuments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFullname();
    }

    public function __get($name): mixed
    {
        if (str_starts_with($name, 'additionalField')) {
            return $this->{$name}();
        }

        return null;
    }

    public function __call($name, $arguments): mixed
    {
        if (str_starts_with($name, 'additionalField')) {
            return $this->getAdditionalFieldValue(substr($name, 15));
        }

        return null;
    }

    public function __set($name, $value): void
    {
        if (str_starts_with($name, 'additionalField')) {
            $this->setAdditionalFieldValue(substr($name, 15), $value);
        }
    }

    public function getPhoto(): ?File
    {
        return $this->photo;
    }

    public function setPhoto(?File $photo = null): self
    {
        $this->photo = $photo;

        if ($photo) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }

        return $this;
    }

    public function getPhotoPathWeb(): string
    {
        return 'uploads/images/client/photo/'.substr($this->getPhotoName(), 0, 2).'/'.$this->getPhotoName();
    }

    public function getPhotoPath(): string
    {
        return __DIR__.'/../../public/'.$this->getPhotoPathWeb();
    }

    public function getPhotoSize(int $width, int $height): array
    {
        if (!$this->isImage()) {
            return [$width, $height];
        }

        [$width_orig, $height_orig] = getimagesize($this->getPhotoPath());
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

    public function getPhotoFileBase64(): string
    {
        if (!$this->isImage()) {
            return '';
        }

        return 'data:image/png;base64,'.base64_encode(file_get_contents($this->getPhotoPath()));
    }

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

    /**
     * Последний договор
     */
    public function getLastContract(): Contract|false
    {
        return $this->contracts->first();
    }

    /**
     * Возвращает объект значения дополнительного поля клиента по коду поля
     */
    public function getAdditionalFieldValueObject(string $fieldCode): ?ClientFieldValue
    {
        foreach ($this->fieldValues as $fieldValue) {
            $field = $fieldValue->getField();

            if ($field->getCode() === $fieldCode) {
                return $fieldValue;
            }
        }

        return null;
    }

    /**
     * Возвращает значение дополнительного поля клиента по коду поля
     */
    public function getAdditionalFieldValue(string $fieldCode): ClientFieldOption|\DateTimeImmutable|Collection|string|null
    {
        $fieldValue = $this->getAdditionalFieldValueObject($fieldCode);

        if ($fieldValue instanceof ClientFieldValue) {
            return $fieldValue->getValue();
        }

        return null;
    }

    /**
     * Подготавливает массивы $additionalFieldValues и $additionalFieldValuesToRemove
     * для обработки в методах prePersist и preUpdate админки App\Admin\ClientAdmin
     */
    public function setAdditionalFieldValue(string $fieldCode, mixed $value): void
    {
        if (empty($value) || ($value instanceof Collection && $value->isEmpty())) {
            $this->additionalFieldValuesToRemove[] = $fieldCode;

            return;
        }

        $this->additionalFieldValues[$fieldCode] = $value;
    }

    /**
     * Есть ли у клиента документ для постановки на учет
     */
    public function hasRegistrationDocument(): bool
    {
        foreach ($this->getDocuments() as $document) {
            $type = $document->getType();

            if (!$type instanceof DocumentType) {
                continue;
            }

            if ($type->getType() === DocumentType::TYPE_REGISTRATION) {
                return true;
            }
        }

        return false;
    }

    public function getInitials(): string
    {
        $initials = '';

        if (!empty($this->firstname)) {
            $initials = $initials.mb_substr($this->firstname, 0, 1).'.';
        }

        if (!empty($this->middlename)) {
            $initials = $initials.mb_substr($this->middlename, 0, 1).'.';
        }

        return $initials;
    }

    /**
     * Фамилия и инициалы
     */
    public function getLastnameAndInitials(): string
    {
        if (empty($this->lastname)) {
            return $this->firstname;
        }

        if (empty($this->firstname)) {
            return $this->lastname;
        }

        return $this->lastname.' '.$this->getInitials();
    }

    public function getPhotoName(): string
    {
        return $this->photoName ?? '';
    }

    public function setPhotoName(?string $photoName): self
    {
        $this->photoName = $photoName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function setBirthPlace(?string $birthPlace): self
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(?int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    public function setMiddlename(?string $middlename): self
    {
        $this->middlename = $middlename;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastResidenceDistrict(): ?District
    {
        return $this->lastResidenceDistrict;
    }

    public function setLastResidenceDistrict(?District $lastResidenceDistrict): self
    {
        $this->lastResidenceDistrict = $lastResidenceDistrict;

        return $this;
    }

    public function getLastRegistrationDistrict(): ?District
    {
        return $this->lastRegistrationDistrict;
    }

    public function setLastRegistrationDistrict(?District $lastRegistrationDistrict): self
    {
        $this->lastRegistrationDistrict = $lastRegistrationDistrict;

        return $this;
    }

    public function getFieldValues(): Collection
    {
        return $this->fieldValues;
    }

    public function addFieldValue(ClientFieldValue $fieldValue): self
    {
        $this->fieldValues->add($fieldValue);

        return $this;
    }

    public function removeFieldValue(ClientFieldValue $fieldValue): void
    {
        $this->fieldValues->removeElement($fieldValue);
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        $this->notes->add($note);

        return $this;
    }

    public function removeNote(Note $note): void
    {
        $this->notes->removeElement($note);
    }

    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): self
    {
        $this->contracts->add($contract);

        return $this;
    }

    public function removeContract(Contract $contract): void
    {
        $this->contracts->removeElement($contract);
    }

    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        $this->documents->add($document);

        return $this;
    }

    public function removeDocument(Document $document): void
    {
        $this->documents->removeElement($document);
    }

    public function getShelterHistories(): Collection
    {
        return $this->shelterHistories;
    }

    public function addShelterHistory(ShelterHistory $shelterHistory): self
    {
        $this->shelterHistories->add($shelterHistory);

        return $this;
    }

    public function removeShelterHistory(ShelterHistory $shelterHistory): void
    {
        $this->shelterHistories->removeElement($shelterHistory);
    }

    public function getDocumentFiles(): Collection
    {
        return $this->documentFiles;
    }

    public function addDocumentFile(DocumentFile $documentFile): self
    {
        $this->documentFiles->add($documentFile);

        return $this;
    }

    public function removeDocumentFile(DocumentFile $documentFile): void
    {
        $this->documentFiles->removeElement($documentFile);
    }

    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        $this->services->add($service);

        return $this;
    }

    public function removeService(Service $service): void
    {
        $this->services->removeElement($service);
    }

    public function getCertificates(): Collection
    {
        return $this->certificates;
    }

    public function addCertificate(Certificate $certificate): self
    {
        $this->certificates->add($certificate);

        return $this;
    }

    public function removeCertificate(Certificate $certificate): void
    {
        $this->certificates->removeElement($certificate);
    }

    public function getGeneratedDocuments(): Collection
    {
        return $this->generatedDocuments;
    }

    public function addGeneratedDocument(GeneratedDocument $generatedDocument): self
    {
        $this->generatedDocuments->add($generatedDocument);

        return $this;
    }

    public function removeGeneratedDocument(GeneratedDocument $generatedDocument): void
    {
        $this->generatedDocuments->removeElement($generatedDocument);
    }

    public function isHomeless(): bool
    {
        return $this->isHomeless;
    }

    public function setIsHomeless(bool $isHomeless): self
    {
        $this->isHomeless = $isHomeless;

        return $this;
    }

    public function setNotIsHomeless(bool $notIsHomeless): self
    {
        $this->isHomeless = !$notIsHomeless;

        return $this;
    }

    public function getClientViews(): Collection
    {
        return $this->clientViews;
    }

    public function setClientViews(Collection $clientViews): self
    {
        $this->clientViews = $clientViews;

        return $this;
    }

    public function getHistoryDownloads(): Collection
    {
        return $this->historyDownloads;
    }

    public function setHistoryDownloads(Collection $historyDownloads): self
    {
        $this->historyDownloads = $historyDownloads;

        return $this;
    }

    private function isImage(): bool
    {
        return file_exists($this->getPhotoPath()) && @\is_array(getimagesize($this->getPhotoPath()));
    }
}
