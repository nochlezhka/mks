<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Заполненная редактируемая форма
 *
* @ORM\Entity
*/
class ClientFormResponse extends BaseEntity
{
    /**
     * Клиент, к которому относится анкета
     *
     * @var Client
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * Форма заполненной анкеты
     *
     * @var ClientForm
     * @ORM\ManyToOne(targetEntity="ClientForm")
     * @ORM\JoinColumn(nullable=false)
     */
    private $form;

    /**
     * Название анкеты
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * Поля заполненной анкеты
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ClientFormResponseValue", mappedBy="clientFormResponse",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $values;

    /**
     * Набор значений полей формы из запроса на создание/обновление заполненной анкеты.
     * Ключ - ID поля, значение - значение поля.
     * Не записывается в базу. Админка перед сохранением заполненной анкеты преобразует массив в набор объектов,
     * который потом будет записан в $values
     *
     * @var array
     */
    private $_submittedFields = [];

    /**
     * ClientFormResponse constructor.
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
    }


    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

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
     * @return ArrayCollection
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param ArrayCollection $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * Магическая функция для чтения значения поля анкеты по её ID.
     * $r->__get('field_2') вернёт значение поля с ID == 2.
     * Все поля получаются через $this->getValues().
     *
     * @param $name
     * @return string|null
     */
    public function __get($name)
    {
        if (substr($name, 0, 6) === 'field_') {
            return $this->getFieldValue(substr($name, 6));
        }
        throw new \LogicException("No field $name in ClientFormResponse");
    }

    private function getFieldValue($fieldId) {
        foreach ($this->getValues() as $value) {
            /**
             * @var $value ClientFormResponseValue
             */
            if ($value->getClientFormField()->getId() == $fieldId) {
                return $value->getValue();
            }
        }
        return null;
    }

    /**
     * Магический метод для админки. Через него выставляются значения полей формы.
     * Значения полей не будут сами сохранены в БД, а будут прикопаны в массив $this->_submittedFields
     *
     * @see _submittedFields
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (substr($name, 0, 6) === 'field_') {
            $this->_submittedFields[substr($name, 6)] = $value;
            return;
        }
        throw new \LogicException("No field $name in ClientFormResponse");
    }

    /**
     * Возвращает массив $this->_submittedFields
     *
     * @see _submittedFields
     * @return array
     */
    public function _getSubmittedFields() {
        return $this->_submittedFields;
    }
}
