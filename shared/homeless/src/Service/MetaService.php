<?php


namespace App\Service;


use App\Entity\Meta;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Чтение и включение флажков
 */
class MetaService
{
    const CLIENT_FORMS_ENABLED = 'client_forms_enabled';

    private ManagerRegistry $managerRegistry;

    private array $booleanMetaCache = [];

    /**
     * Разрешено ли использование анкеты проживающего в новом формате.
     *
     * Включается с помощью консольной команды `homeless:resident_qnr:check_and_switch`
     *
     * @return bool
     */
    public function isClientFormsEnabled(): bool
    {
        return $this->getCachedBooleanMeta(self::CLIENT_FORMS_ENABLED);
    }

    public function enableClientForms(): void
    {
        $this->setCachedBooleanMeta(self::CLIENT_FORMS_ENABLED, true);
    }

    private function getCachedBooleanMeta(string $name): bool
    {
        if (isset($this->booleanMetaCache[$name])) {
            return $this->booleanMetaCache[$name];
        }
        $arr = $this->managerRegistry->getRepository(Meta::class)->findBy(['key' => $name]);
        if (count($arr) == 0) {
            $this->booleanMetaCache[$name] = false;
            return false;
        }
        $meta = $arr[0];
        /**
         * @var Meta $meta
         */
        $this->booleanMetaCache[$name] = !!$meta->getValue();
        return $this->booleanMetaCache[$name];
    }

    private function setCachedBooleanMeta(string $name, bool $value): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $arr = $entityManager->getRepository(Meta::class)->findBy(['key' => self::CLIENT_FORMS_ENABLED]);
        /**
         * @var Meta $meta
         */
        $meta = null;
        if (count($arr) == 0) {
            $meta = new Meta();
            $meta->setKey(self::CLIENT_FORMS_ENABLED);
            $entityManager->persist($meta);
        } else {
            $meta = $arr[0];
        }
        $meta->setValue(!!$value ? '1' : '0');
        $this->booleanMetaCache[$name] = !!$value;
    }

    #[Required]
    public function setManagerRegistry(ManagerRegistry $managerRegistry): void
    {
        $this->managerRegistry = $managerRegistry;
    }
}
