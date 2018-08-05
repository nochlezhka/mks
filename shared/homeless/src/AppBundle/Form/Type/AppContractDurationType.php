<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AppContractDurationType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'app_contract_duration';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }


    public function getParent()
    {
        return NumberType::class;
    }
}
