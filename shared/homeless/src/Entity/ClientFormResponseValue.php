<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Значение поля анкеты.
 * Значение хранится в виде строки даже если тип поля - чекбокс или выбор варианта.
 * Для чекбоксов хранится `'1'` или `'0'`, а для выбора варианта - текст самого варианта.
 * Так проще и, возможно, поможет не потерять данные.
 */
#[ORM\Entity]
#[ORM\UniqueConstraint(name: "client_form_response_uniq", columns: ["client_form_response_id", "client_form_field_id"])]
class ClientFormResponseValue extends BaseEntity
{
    // константы-значения фиксированных полей
    const RESIDENT_QUESTIONNAIRE_TYPE_3_MONTHS = "3 месяца";
    const RESIDENT_QUESTIONNAIRE_TYPE_6_MONTHS = "6 месяцев";
    const RESIDENT_QUESTIONNAIRE_TYPE_1_YEAR = "1 год";
    const RESIDENT_QUESTIONNAIRE_TYPE_2_YEARS = "2 года";

    /**
     * Анкета
     */
    #[ORM\ManyToOne(targetEntity: ClientFormResponse::class, inversedBy: "values")]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientFormResponse $clientFormResponse = null;

    /**
     * Поле формы
     */
    #[ORM\ManyToOne(targetEntity: ClientFormField::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientFormField $clientFormField = null;

    /**
     * Клиент.
     * Эта информация дублируется с `ClientFormResponse::client` сознательно для того, чтобы было легче делать запрос
     * на поиск по значениям полей всех анкет клиента.
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    /**
     * Значение поля
     */
    #[ORM\Column(type: "text", nullable: false)]
    private string $value = "";

    /**
     * @return ClientFormResponse
     */
    public function getClientFormResponse(): ?ClientFormResponse
    {
        return $this->clientFormResponse;
    }

    /**
     * @param ClientFormResponse $clientFormResponse
     */
    public function setClientFormResponse(ClientFormResponse $clientFormResponse)
    {
        $this->clientFormResponse = $clientFormResponse;
    }

    /**
     * @return ClientFormField
     */
    public function getClientFormField(): ?ClientFormField
    {
        return $this->clientFormField;
    }

    /**
     * @param ClientFormField $clientFormField
     */
    public function setClientFormField(ClientFormField $clientFormField)
    {
        $this->clientFormField = $clientFormField;
    }

    /**
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }
}
