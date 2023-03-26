<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use App\Repository\ClientFormRepository;
use App\Util\BaseEntityUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Редактируемая форма
 */
#[ORM\Entity(repositoryClass: ClientFormRepository::class)]
class ClientForm extends BaseEntity
{
    /**
     * Зарезервировано под форму анкеты проживающего.
     * Форма с этим ID создаётся миграцией, и не может быть удалена.
     */
    public const RESIDENT_QUESTIONNAIRE_FORM_ID = 1;

    #[ORM\Column(type: 'string')]
    private string $name = '';

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: ClientFormField::class)]
    private Collection $fields;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * Возвращает текстовое представление объекта для breadcrumbs
     */
    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function setFields(Collection $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * Возвращает первое поле формы, если отсортировать поля по значению `sort`.
     *
     * Если у формы нет полей, то возвращает `null`
     */
    public function getFirstField(): ?ClientFormField
    {
        /** @var array<ClientFormField> $formFields */
        $formFields = $this->getFields()->toArray();
        BaseEntityUtil::sortEntities($formFields);
        if (\count($formFields) === 0) {
            return null;
        }

        return $formFields[0];
    }
}
