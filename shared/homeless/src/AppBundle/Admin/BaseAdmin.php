<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;

class BaseAdmin extends AbstractAdmin
{
    use BaseAdminTrait;

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        $result = $name;

        switch ($name){
            case 'edit':
                $name = ':CRUD:base_edit.html.twig';
                break;
            default:
                $name = parent::getTemplate($name);
                break;
        }

        return $name;
    }

    public function getBatchActions()
    {
        return [];
    }

    public function getParameter($name)
    {
        return $this->getConfigurationPool()->getContainer()->getParameter($name);
    }
}
