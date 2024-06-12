<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeImmutableToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToArrayTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppHomelessFromDateType extends AbstractType
{
    public const int DEFAULT_FORMAT = \IntlDateFormatter::MEDIUM;

    public const string HTML5_FORMAT = 'yyyy-MM';

    private static array $acceptedFormats = [
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::LONG,
        \IntlDateFormatter::MEDIUM,
        \IntlDateFormatter::SHORT,
    ];

    private static array $widgets = [
        'text' => TextType::class,
        'choice' => ChoiceType::class,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateFormat = \is_int($options['format']) ? $options['format'] : self::DEFAULT_FORMAT;
        $timeFormat = \IntlDateFormatter::NONE;
        $calendar = \IntlDateFormatter::GREGORIAN;
        $pattern = \is_string($options['format']) ? $options['format'] : null;

        if (!\in_array($dateFormat, self::$acceptedFormats, true)) {
            throw new InvalidOptionsException('The "format" option must be one of the IntlDateFormatter constants (FULL, LONG, MEDIUM, SHORT) or a string representing a custom format.');
        }

        if ($options['widget'] === 'single_text') {
            if ($pattern !== null && !str_contains($pattern, 'y') && !str_contains($pattern, 'M') && !str_contains($pattern, 'd')) {
                throw new InvalidOptionsException(sprintf('The "format" option should contain the letters "y", "M" or "d". Its current value is "%s".', $pattern));
            }

            $builder->addViewTransformer(new DateTimeToLocalizedStringTransformer(
                $options['model_timezone'],
                $options['view_timezone'],
                $dateFormat,
                $timeFormat,
                $calendar,
                $pattern,
            ));
        } else {
            if ($pattern !== null && (!str_contains($pattern, 'y') || !str_contains($pattern, 'M') || !str_contains($pattern, 'd'))) {
                throw new InvalidOptionsException(sprintf('The "format" option should contain the letters "y", "M" and "d". Its current value is "%s".', $pattern));
            }

            $yearOptions = $monthOptions = [
                'error_bubbling' => true,
            ];

            try {
                $formatter = new \IntlDateFormatter(
                    \Locale::getDefault(),
                    $dateFormat,
                    $timeFormat,
                    null,
                    $calendar,
                    $pattern,
                );
            } catch (\Throwable $e) {
                throw new InvalidOptionsException($e->getMessage(), $e->getCode());
            }

            $formatter->setLenient(false);

            if ($options['widget'] === 'choice') {
                // Only pass a subset of the options to children
                $yearOptions['choices'] = $this->formatTimestamps($formatter, '/y+/', $this->listYears($options['years']));
                $yearOptions['placeholder'] = $options['placeholder']['year'];
                $yearOptions['choice_translation_domain'] = $options['choice_translation_domain']['year'];
                $monthOptions['choices'] = $this->formatTimestamps($formatter, '/[M|L]+/', $this->listMonths($options['months']));
                $monthOptions['placeholder'] = $options['placeholder']['month'];
                $monthOptions['choice_translation_domain'] = $options['choice_translation_domain']['month'];
            }

            // Append generic carry-along options
            foreach (['required', 'translation_domain'] as $passOpt) {
                $yearOptions[$passOpt] = $monthOptions[$passOpt] = $options[$passOpt];
            }

            $builder
                ->add('year', self::$widgets[$options['widget']], $yearOptions)
                ->add('month', self::$widgets[$options['widget']], $monthOptions)
                ->addViewTransformer(new DateTimeToArrayTransformer(
                    $options['model_timezone'], $options['view_timezone'], ['year', 'month'],
                ))
                ->setAttribute('formatter', $formatter)
            ;
        }

        switch ($options['input']) {
            case 'datetime_immutable':
                $builder->addModelTransformer(new DateTimeImmutableToDateTimeTransformer());
                break;

            case 'string':
                $builder->addModelTransformer(new DateTimeToStringTransformer($options['model_timezone'], $options['model_timezone'], 'Y-m-d'));
                break;

            case 'timestamp':
                $builder->addModelTransformer(new DateTimeToTimestampTransformer($options['model_timezone'], $options['model_timezone']));
                break;

            case 'array':
                $builder->addModelTransformer(new DateTimeToArrayTransformer($options['model_timezone'], $options['model_timezone'], ['year', 'month']));
                break;
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['widget'] = $options['widget'];

        // Change the input to a HTML5 date input if
        //  * the widget is set to "single_text"
        //  * the format matches the one expected by HTML5
        //  * the html5 is set to true
        if ($options['html5'] && $options['widget'] === 'single_text' && $options['format'] === self::HTML5_FORMAT) {
            $view->vars['type'] = 'date';
        }

        if (!$form->getConfig()->hasAttribute('formatter')) {
            return;
        }

        $pattern = $form->getConfig()->getAttribute('formatter')->getPattern();

        // remove special characters unless the format was explicitly specified
        if (!\is_string($options['format'])) {
            // remove quoted strings first
            $pattern = preg_replace('/\'[^\']+\'/', '', $pattern);

            // remove remaining special chars
            $pattern = preg_replace('/[^yMd]+/', '', $pattern);
        }

        // set right order with respect to locale (e.g.: de_DE=dd.MM.yy; en_US=M/d/yy)
        // lookup various formats at http://userguide.icu-project.org/formatparse/datetime
        $pattern = preg_match('/^([yMd]+)[^yMd]*([yMd]+)[^yMd]*([yMd]+)$/', $pattern)
            ? preg_replace(['/y+/', '/M+/', '/d+/'], ['{{ year }}', '{{ month }}'], $pattern)
            : '{{ year }}{{ month }}'; // default fallback

        $view->vars['date_pattern'] = $pattern;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $compound = static fn (Options $options) => $options['widget'] !== 'single_text';

        $placeholder = $placeholderDefault = static fn (Options $options) => $options['required'] ? null : '';

        $placeholderNormalizer = static function (Options $options, $placeholder) use ($placeholderDefault) {
            if (\is_array($placeholder)) {
                $default = $placeholderDefault($options);

                return [
                    ...['year' => $default, 'month' => $default],
                    ...$placeholder,
                ];
            }

            return [
                'year' => $placeholder,
                'month' => $placeholder,
            ];
        };

        $choiceTranslationDomainNormalizer = static function (Options $options, $choiceTranslationDomain) {
            if (\is_array($choiceTranslationDomain)) {
                return array_replace(
                    ['year' => false, 'month' => false],
                    $choiceTranslationDomain,
                );
            }

            return [
                'year' => $choiceTranslationDomain,
                'month' => $choiceTranslationDomain,
            ];
        };

        $format = static fn (Options $options) => $options['widget'] === 'single_text' ? self::HTML5_FORMAT : self::DEFAULT_FORMAT;

        $year = (int) date('Y');
        $resolver->setDefaults([
            'years' => range($year - 5, $year + 5),
            'months' => range(1, 12),
            'widget' => 'choice',
            'input' => 'datetime_immutable',
            'format' => $format,
            'model_timezone' => null,
            'view_timezone' => null,
            'placeholder' => $placeholder,
            'html5' => true,
            // Don't modify \DateTime classes by reference, we treat
            // them like immutable value objects
            'by_reference' => false,
            'error_bubbling' => false,
            // If initialized with a \DateTime object, FormType initializes
            // this option to "\DateTime". Since the internal, normalized
            // representation is not \DateTime, but an array, we need to unset
            // this option.
            'data_class' => null,
            'compound' => $compound,
            'choice_translation_domain' => false,
        ]);

        $resolver->setNormalizer('placeholder', $placeholderNormalizer);
        $resolver->setNormalizer('choice_translation_domain', $choiceTranslationDomainNormalizer);

        $resolver->setAllowedValues('input', [
            'datetime_immutable',
            'string',
            'timestamp',
            'array',
        ]);
        $resolver->setAllowedValues('widget', [
            'single_text',
            'text',
            'choice',
        ]);

        $resolver->setAllowedTypes('format', ['int', 'string']);
        $resolver->setAllowedTypes('years', 'array');
        $resolver->setAllowedTypes('months', 'array');
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix(): string
    {
        return 'app_homeless_from_date';
    }

    private function formatTimestamps(\IntlDateFormatter $formatter, $regex, array $timestamps): array
    {
        $pattern = $formatter->getPattern();
        $timezone = $formatter->getTimezoneId();
        $formattedTimestamps = [];

        $formatter->setTimeZone('UTC');

        if (preg_match($regex, $pattern, $matches)) {
            $formatter->setPattern($matches[0]);

            foreach ($timestamps as $timestamp => $choice) {
                $formattedTimestamps[$formatter->format($timestamp)] = $choice;
            }

            // I'd like to clone the formatter above, but then we get a
            // segmentation fault, so let's restore the old state instead
            $formatter->setPattern($pattern);
        }

        $formatter->setTimeZone($timezone);

        return $formattedTimestamps;
    }

    private function listYears(array $years): array
    {
        $result = [];

        foreach ($years as $year) {
            $y = gmmktime(0, 0, 0, 6, 15, $year);
            if ($y !== false) {
                $result[$y] = $year;
            }
        }

        return $result;
    }

    private function listMonths(array $months): array
    {
        $result = [];

        foreach ($months as $month) {
            $result[gmmktime(0, 0, 0, $month, 15)] = $month;
        }

        return $result;
    }
}
