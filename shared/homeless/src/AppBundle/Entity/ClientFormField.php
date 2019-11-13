<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Поле настраиваемой формы
 *
 * @ORM\Entity
 */
class ClientFormField extends BaseEntity
{
    const TYPE_TEXT = 1;
    const TYPE_OPTION = 2;
    const TYPE_CHECKBOX = 3;

    /**
     * Форма, которой принадлежит поле
     *
     * @var ClientForm
     * @ORM\ManyToOne(targetEntity="ClientForm", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $form;

    /**
     * Название поля
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * Тип поля (см. константы класса, TYPE_*)
     *
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * Список вариантов для выбора в поле типа TYPE_OPTION.
     * Варианты разделены переводом строки.
     * Не имеет значения для других типов поля.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $options;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $required;

    /**
     * @return ClientForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param ClientForm $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }
}
