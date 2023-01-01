<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;

class BaseAdmin extends AbstractAdmin
{
    use BaseAdminTrait;

    public function getParameter($name)
    {
        return $this->getConfigurationPool()->getContainer()->getParameter($name);
    }
}
