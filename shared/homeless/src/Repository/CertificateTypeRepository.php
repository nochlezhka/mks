<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Certificate;
use App\Entity\CertificateType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CertificateType|null   find($id, $lockMode = null, $lockVersion = null)
 * @method CertificateType|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<CertificateType> findAll()
 * @method array<CertificateType> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificateTypeRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, CertificateType::class);
    }

    /**
     * Получение доступных типов для сертификата
     *
     * @return array<CertificateType>
     */
    public function getAvailableForCertificate(Certificate $certificate): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->orderBy('t.sort', 'ASC')
            ->where('t.syncId IN (:types)')
            ->setParameter('types', array_values([
                CertificateType::REGISTRATION,
                CertificateType::TRAVEL,
            ]))
        ;

        if (!$certificate->getClient()->hasRegistrationDocument()) {
            $queryBuilder
                ->andWhere('t.syncId != :regType')
                ->setParameter('regType', CertificateType::REGISTRATION)
            ;
        }

        return $queryBuilder->getQuery()->getResult() ?? [];
    }
}
