<?php


namespace AppBundle\Service;


use AppBundle\Entity\Meta;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class MetaService
{
    const CLIENT_FORMS_ENABLED = 'client_forms_enabled';

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
        $this->metaRepository = $entityManager->getRepository(Meta::class);
    }

    public function isClientFormsEnabled()
    {
        return $this->getCachedBooleanMeta(self::CLIENT_FORMS_ENABLED);
    }

    /**
     * @param $name
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
}