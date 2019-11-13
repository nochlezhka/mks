<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Значение поля анкеты.
 * Значение хранится в виде строки даже если тип поля - чекбокс или выбор варианта.
 * Для чекбоксов хранится `'1'` или `'0'`, а для выбора варианта - текст самого варианта.
 * Так проще и, возможно, поможет не потерять данные.
 *
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @UniqueConstraint(name="client_form_response_uniq", columns={"client_form_response_id", "client_form_field_id"})
 * })
 */
class ClientFormResponseValue extends BaseEntity
{
    /**
     * Анкета
     *
     * @var ClientFormResponse
     * @ORM\ManyToOne(targetEntity="ClientFormResponse", inversedBy="values")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientFormResponse;

    /**
     * Поле формы
     *
     * @var ClientFormField
     * @ORM\ManyToOne(targetEntity="ClientFormField")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientFormField;

    /**
     * Значение поля
     *
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $value;

    /**
     * @return ClientFormResponse
     */
    public function getClientFormResponse()
    {
        return $this->clientFormResponse;
    }

    /**
     * @param ClientFormResponse $clientFormResponse
     */
    public function setClientFormResponse($clientFormResponse)
    {
        $this->clientFormResponse = $clientFormResponse;
    }

    /**
     * @return ClientFormField
     */
    public function getClientFormField()
    {
        return $this->clientFormField;
    }

    /**
     * @param ClientFormField $clientFormField
     */
    public function setClientFormField($clientFormField)
    {
        $this->clientFormField = $clientFormField;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
