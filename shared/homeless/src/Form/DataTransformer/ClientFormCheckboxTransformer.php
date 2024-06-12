<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Преобразователь поля из текста в boolean.
 * Нужен для того, чтобы отрисовывать текстовое поле настраиваемой формы как чекбокс.
 */
final readonly class ClientFormCheckboxTransformer implements DataTransformerInterface
{
    /**
     * @param int|null $formResponseId ID формы для сообщений об ошибке
     * @param int      $formFieldId    ID поля формы для сообщений об ошибке
     */
    public function __construct(
        private ?int $formResponseId,
        private int $formFieldId,
    ) {}

    public function transform($value): bool
    {
        if ($value !== '0' && $value !== false && $value !== null && $value !== '1' && $this->formResponseId !== null) {
            error_log(sprintf('Non-boolean value in form response %d: field %d has value %s',
                $this->formResponseId, $this->formFieldId, var_export($value, true),
            ));
        }

        return (bool) $value;
    }

    public function reverseTransform($value): ?string
    {
        return $value
            ? '1'
            : null; // если вернуть null, незаполненное поле не будет создано
    }
}
