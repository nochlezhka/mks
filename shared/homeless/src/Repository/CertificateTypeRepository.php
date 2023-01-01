<?php

namespace App\Repository;

use App\Entity\Certificate;
use App\Entity\CertificateType;
use Doctrine\ORM\EntityRepository;

class CertificateTypeRepository extends EntityRepository
{
    /**
     * Получение доступных типов для сертификата
     *
     * @param Certificate $certificate
     * @return CertificateType[]
     */
    public function getAvailableForCertificate(Certificate $certificate)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->orderBy('t.sort', 'ASC')
            ->where('t.syncId IN (:types)')
            ->setParameter('types', array_values([
                CertificateType::REGISTRATION,
                CertificateType::TRAVEL,
            ]));

        if (!$certificate->getClient()->hasRegistrationDocument()) {
            $qb
                ->andWhere('t.syncId != :regType')
                ->setParameter('regType', CertificateType::REGISTRATION);
        }

        $result = $qb->getQuery()->execute();

        return null === $result ? [] : $result;
    }
}
