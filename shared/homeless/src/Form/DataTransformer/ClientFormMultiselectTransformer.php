<?php


namespace App\Form\DataTransformer;


use App\Util\ClientFormUtil;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Преобразователь поля из текста в массив строк и обратно.
 * Нужен для того, чтобы отрисовывать текстовое поле настраиваемой формы как селект со множественным выбором.
 */
class ClientFormMultiselectTransformer implements DataTransformerInterface
{
    private $formResponseId;
    private $formFieldId;

    /**
     * ClientFormMultiselectTransformer constructor.
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
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            error_log("Non-string value in multiselect transform. Form response  " .
                $this->formResponseId . ": field " . $this->formFieldId . " has value ".var_export($value, true)
            );
        }
        return ClientFormUtil::optionsTextToArray($value);
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            error_log("Non-array value in multiselect reverseTransform. Form response  " .
                $this->formResponseId . ": field " . $this->formFieldId . " has value ".var_export($value, true)
            );
        }
        if (is_array($value) && count($value) > 0) {
            return ClientFormUtil::arrayToOptionsText($value);
        }
        // если вернуть null, незаполненное поле не будет создано
        return null;
    }
}
