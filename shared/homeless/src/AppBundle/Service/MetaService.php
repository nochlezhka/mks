<?php


namespace AppBundle\Service;


use AppBundle\Entity\Meta;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Чтение и включение флажков
 * @package AppBundle\Service
 */
class MetaService
{
    const CLIENT_FORMS_ENABLED = 'client_forms_enabled';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $metaRepository;

    private $booleanMetaCache = [];

    /**
     * MetaService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->metaRepository = $entityManager->getRepository(Meta::class);
    }

    /**
     * Разрешено ли использование анкеты проживающего в новом формате.
     *
     * Включается с помощью консольной команды `homeless:resident_qnr:check_and_switch`
     *
     * @return bool
     */
    public function isClientFormsEnabled()
    {
        return $this->getCachedBooleanMeta(self::CLIENT_FORMS_ENABLED);
    }

    public function enableClientForms()
    {
        $this->setCachedBooleanMeta(self::CLIENT_FORMS_ENABLED, true);
    }

    /**
     * @param string $name
     * @return bool
     */
    private function getCachedBooleanMeta($name)
    {
        if (isset($this->booleanMetaCache[$name])) {
            return $this->booleanMetaCache[$name];
        }
        $arr = $this->metaRepository->findBy(['key' => $name]);
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

    /**
     * @param string $name
     * @param bool $value
     */
    private function setCachedBooleanMeta($name, $value)
    {
        $arr = $this->metaRepository->findBy(['key' => self::CLIENT_FORMS_ENABLED]);
        /**
         * @var Meta $meta
         */
        $meta = null;
        if (count($arr) == 0) {
            $meta = new Meta();
            $meta->setKey(self::CLIENT_FORMS_ENABLED);
            $this->entityManager->persist($meta);
        } else {
            $meta = $arr[0];
        }
        $meta->setValue(!!$value ? '1' : '0');
        $this->booleanMetaCache[$name] = !!$value;
    }
}
