<?php

namespace AppBundle\Admin;

use AppBundle\Entity\ContractStatus;

trait BaseAdminTrait
{

    public function getMyClientsNoticeCount()
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();

        $filter = ['contractCreatedBy' => $user->getId()];

        $inProcessStatus = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:ContractStatus')
            ->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);

        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string)$inProcessStatus->getId()]];
        }

        $arNoticesId = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Notice')
            ->getMyClientsNoticeHeaderCount($filter, $user);

        return $arNoticesId;
    }

    /**
     * Получение заголовков оповещений клиентов для текущего пользователя
     *
     * @return mixed
     */
    public function getMyClientsNoticeHeader()
    {
        $user = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('security.context')
            ->getToken()
            ->getUser();

        $filter = ['contractCreatedBy' => $user->getId()];

        $inProcessStatus = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:ContractStatus')
            ->findOneBy(['syncId' => ContractStatus::IN_PROCESS]);

        if ($inProcessStatus instanceof ContractStatus) {
            $filter['contractStatus'] = ['value' => [(string)$inProcessStatus->getId()]];
        }

        $arNoticesId = $this
            ->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Notice')
            ->getMyClientsNoticeHeader($filter, $user);

        return $arNoticesId;
    }
}
