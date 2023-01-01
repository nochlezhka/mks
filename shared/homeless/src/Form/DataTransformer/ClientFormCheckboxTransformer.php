<?php


namespace App\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;

/**
 * Преобразователь поля из текста в boolean.
 * Нужен для того, чтобы отрисовывать текстовое поле настраиваемой формы как чекбокс.
 */
class ClientFormCheckboxTransformer implements DataTransformerInterface
{
    private $formResponseId;
    private $formFieldId;

    /**
     * ClientFormCheckboxTransformer constructor.
     * @param integer|null $formResponseId ID формы для сообщений об ошибке
     * @param integer $formFieldId ID поля формы для сообщений об ошибке
     */
    public function __construct($formResponseId, $formFieldId)
    {
        $this->formResponseId = $formResponseId;
        $this->formFieldId = $formFieldId;
    }


    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if ($value !== '0' && $value !== false && $value !== null && $value !== '1' && $this->formResponseId !== null) {
            error_log("Non-boolean value in form response " .
                $this->formResponseId . ": field " . $this->formFieldId . " has value ".
                ($value === null ? "null" : "'$value'").var_export($value, true)
            );
        }
        return !!$value;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        if ($value) {
            return '1';
        }
        // если вернуть null, незаполненное поле не будет создано
        return null;
    }
}
