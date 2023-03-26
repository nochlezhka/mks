<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Form\DataTransformer;

use App\Util\ClientFormUtil;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Преобразователь поля из текста в массив строк и обратно.
 * Нужен для того, чтобы отрисовывать текстовое поле настраиваемой формы как селект со множественным выбором.
 */
readonly class ClientFormMultiselectTransformer implements DataTransformerInterface
{
    /**
     * @param int|null $formResponseId ID формы для сообщений об ошибке
     * @param int      $formFieldId    ID поля формы для сообщений об ошибке
     */
    public function __construct(
        private ?int $formResponseId,
        private int $formFieldId,
    ) {}

    public function transform($value): ?array
    {
        if ($value === null) {
            return null;
        }
        if (!\is_string($value)) {
            error_log(sprintf('Non-string value in multiselect transform. Form response %d: field %d has value %s',
                $this->formResponseId, $this->formFieldId, var_export($value, true),
            ));
        }

        return ClientFormUtil::optionsTextToArray($value);
    }

    public function reverseTransform($value): ?string
    {
        if (!\is_array($value)) {
            error_log(sprintf('Non-array value in multiselect reverseTransform. Form response %d: field %d has value %s',
                $this->formResponseId, $this->formFieldId, var_export($value, true),
            ));
        }

        return \is_array($value) && \count($value) > 0
            ? ClientFormUtil::arrayToOptionsText($value)
            : null; // если вернуть null, незаполненное поле не будет создано
    }
}
