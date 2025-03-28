<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ClientFormResponseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Заполненная редактируемая форма
 */
#[ORM\Entity(repositoryClass: ClientFormResponseRepository::class)]
class ClientFormResponse extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Client $client = null;

    /**
     * Форма заполненной анкеты
     */
    #[ORM\ManyToOne(targetEntity: ClientForm::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientForm $form = null;

    /**
     * Поля заполненной анкеты
     */
    #[ORM\OneToMany(
        targetEntity: ClientFormResponseValue::class,
        mappedBy: 'clientFormResponse',
        cascade: ['persist', 'remove', 'detach'],
        orphanRemoval: true,
    )]
    private Collection $values;

    /**
     * Набор значений полей формы из запроса на создание/обновление заполненной анкеты.
     * Заполняется через магический метод `__set`.
     *
     * Ключ - ID поля, значение - значение поля.
     * Не записывается в базу. Админка перед сохранением заполненной анкеты преобразует массив в набор объектов,
     * который потом будет записан в $values
     */
    private array $_submittedFields = [];

    private ?ClientFormField $cachedFirstField = null;

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFirstFieldValue() ?? '';
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getForm(): ?ClientForm
    {
        return $this->form;
    }

    public function setForm(ClientForm $form): void
    {
        $this->form = $form;
    }

    public function getValues(): Collection
    {
        return $this->values;
    }

    public function setValues(Collection $values): void
    {
        $this->values = $values;
    }

    /**
     * Возвращает массив `$this->_submittedFields`
     *
     * @see _submittedFields
     */
    public function _getSubmittedFields(): array
    {
        return $this->_submittedFields;
    }

    /**
     * Возвращает значение первого поля заполненной формы.
     * Первое поле вычисляется хитрым образом с кешированием, см функцию `getCachedFirstField`.
     *
     * @see ClientFormResponse::getCachedFirstField()
     */
    public function getFirstFieldValue(): ?string
    {
        $firstField = $this->getCachedFirstField();
        if ($firstField === null) {
            return null;
        }

        return $this->getFieldValue($firstField->getId());
    }

    /**
     * Вычисляет, заполнена ли анкета.
     * Анкета считается заполненной, если в ней заполнено хотя бы одно поле не считая первого.
     * Первое поле вычисляется хитрым образом с кешированием, см функцию `getCachedFirstField`.
     *
     * @see ClientFormResponse::getCachedFirstField()
     */
    public function isFull(): bool
    {
        $firstFieldId = $this->getCachedFirstField()?->getId();

        foreach ($this->getValues() as $value) {
            /**
             * @var ClientFormResponseValue $value
             */
            if ($value->getClientFormField()->getId() !== $firstFieldId && $value->getValue() !== null) {
                return true;
            }
        }

        return false;
    }

    public function getFieldValue(?int $fieldId): ?string
    {
        /** @var ClientFormResponseValue $value */
        foreach ($this->getValues() as $value) {
            if ($value->getClientFormField()->getId() === $fieldId) {
                return $value->getValue();
            }
        }

        return null;
    }

    public function setFieldValue(int $fieldId, mixed $value): void
    {
        $this->_submittedFields[$fieldId] = $value;
    }

    /**
     * Возвращает закешированное первое поле текущей формы.
     * Первое поле вычисляется по сортировке по значению `sort` полей формы, и кешируется на всё время жизни
     * объекта-анкеты.
     */
    private function getCachedFirstField(): ?ClientFormField
    {
        if ($this->cachedFirstField !== null) {
            return $this->cachedFirstField;
        }

        $form = $this->getForm();
        if ($form === null) {
            return null;
        }
        $this->cachedFirstField = $form->getFirstField();

        return $this->cachedFirstField;
    }
}
