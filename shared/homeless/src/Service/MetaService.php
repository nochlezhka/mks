<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Service;

use App\Entity\Meta;
use App\Repository\MetaRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Чтение и включение флажков
 */
class MetaService
{
    public const CLIENT_FORMS_ENABLED = 'client_forms_enabled';

    private array $booleanMetaCache = [];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MetaRepository $metaRepository,
    ) {}

    /**
     * Разрешено ли использование анкеты проживающего в новом формате.
     */
    public function isClientFormsEnabled(): bool
    {
        return $this->getCachedBooleanMeta();
    }

    public function enableClientForms(): void
    {
        $this->setCachedBooleanMeta();
    }

    private function getCachedBooleanMeta(): bool
    {
        if (isset($this->booleanMetaCache[self::CLIENT_FORMS_ENABLED])) {
            return $this->booleanMetaCache[self::CLIENT_FORMS_ENABLED];
        }
        $arr = $this->metaRepository->findBy(['key' => self::CLIENT_FORMS_ENABLED]);
        if (\count($arr) === 0) {
            $this->booleanMetaCache[self::CLIENT_FORMS_ENABLED] = false;

            return false;
        }
        /** @var Meta $meta */
        $meta = $arr[0];
        $this->booleanMetaCache[self::CLIENT_FORMS_ENABLED] = (bool) $meta->getValue();

        return $this->booleanMetaCache[self::CLIENT_FORMS_ENABLED];
    }

    private function setCachedBooleanMeta(): void
    {
        $arr = $this->metaRepository->findBy(['key' => self::CLIENT_FORMS_ENABLED]);
        if (\count($arr) === 0) {
            $meta = new Meta();
            $meta->setKey(self::CLIENT_FORMS_ENABLED);
            $this->entityManager->persist($meta);
        } else {
            $meta = $arr[0];
        }
        $meta->setValue(true ? '1' : '0');
        $this->booleanMetaCache[self::CLIENT_FORMS_ENABLED] = true;
    }
}
