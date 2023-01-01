<?php


namespace App\Entity;

use App\Util\BaseEntityUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Редактируемая форма
 *
 * @ORM\Entity(repositoryClass="App\Repository\ClientFormRepository")
 */
class ClientForm extends BaseEntity
{
    /**
     * Зарезервировано под форму анкеты проживающего.
     * Форма с этим ID создаётся миграцией, и не может быть удалена.
     */
    const RESIDENT_QUESTIONNAIRE_FORM_ID = 1;

    /**
     * Название
     * @var string
     * @ORM\Column(type="string")
     */
    private string $name = "";

    /**
     * Набор полей
     * @var Collection
     * @ORM\OneToMany(targetEntity="ClientFormField", mappedBy="form")
     */
    private Collection $fields;

    /**
     * ClientForm constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Collection $fields
     */
    public function setFields(Collection $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Возвращает текстовое представление объекта для breadcrumbs
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Возвращает первое поле формы, если отсортировать поля по значению `sort`.
     *
     * Если у формы нет полей, то возвращает `null`
     *
     * @return ClientFormField|null
     */
    public function getFirstField(): ?ClientFormField
    {
        $formFields = $this->getFields()->toArray();
        /**
         * @var $formFields ClientFormField[]
         */
        BaseEntityUtil::sortEntities($formFields);
        if (count($formFields) == 0) {
            return null;
        }
        return $formFields[0];
    }
}
