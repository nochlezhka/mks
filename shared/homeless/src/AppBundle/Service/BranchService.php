<?php


namespace AppBundle\Service;


use AppBundle\Entity\Branch;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BranchService
{
    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

       /**
     * Возвращает объект отделения, где работает сотрудник `$user`,
     * или объект псевдоотделения с шаблонными параметрами из parameters.yml.
     * У объекта псевдоотделения кроме шаблонных параметров не заполнены никакие другие важные поля.
     *
     * @param $user
     * @return Branch
     */

    public function getUserBranchOrDefault($user)
    {
        if (!$user instanceof User || $user->getBranch() === null) {
            return $this->getDefaultBranch();
        }
        return $user->getBranch();
    }

    /**
     * Возвращает словарь с шаблоннами параметрами отделения,
     * где названию поля объекта соответствует название параметра из файла parameters.yml
     *
     * @return string[]
     */
    public static function getTemplateFieldsMap()
    {
        return [
            'orgNameShort' => 'org_name_short',
            'orgName' => 'org_name',
            'orgDescription' => 'org_description',
            'orgDescriptionShort' => 'org_description_short',
            'orgContactsFull' => 'org_contacts_full',
            'orgCity' => 'org_city',
            'dispensaryName' => 'dispensary_name',
            'dispensaryAddress' => 'dispensary_address',
            'dispensaryPhone' => 'dispensary_phone',
            'employmentName' => 'employment_name',
            'employmentAddress' => 'employment_address',
            'employmentInspection' => 'employment_inspection',
            'sanitationName' => 'sanitation_name',
            'sanitationAddress' => 'sanitation_address',
            'sanitationTime' => 'sanitation_time',
        ];
    }

    /**
     * Перезаписывает шаблонные параметры в массиве `$ctx` значениями из объекта отделения.
     * Возвращает массив с перезаписанными параметрами.
     *
     * Если у отделения не выставлено ни одного шаблонного параметра, они не перезаписываются.
     *
     * @param Branch $branch
     * @param array $ctx
     * @return array
     */
    public static function mergeTemplateGlobals(Branch $branch, array $ctx)
    {
        foreach (self::getTemplateFieldsMap() as $fieldName => $paramName) {
            $getter = 'get' . ucfirst($fieldName);
            $value = $branch->$getter();
            if ($paramName == 'org_contacts_full') {
                $value = explode("\n", $value);
            }
            $ctx[$paramName] = $value;
        }
        return $ctx;
    }

    /**
     * Возвращает объект псевдоотделения, где все шаблонные параметры заполнены из значений из parameters.yml.
     * Кроме шаблонных параметров у объекта не заполнены никакие другие важные поля.
     *
     * @return Branch
     */
    public function getDefaultBranch()
    {
        $b = new Branch();
        foreach (self::getTemplateFieldsMap() as $fieldName => $paramName) {
            $value = $this->serviceContainer->getParameter($paramName);
            if ($paramName == 'org_contacts_full') {
                $value = join("\n", $value);
            }
            $setter = 'set' . ucfirst($fieldName);
            $b->$setter($value);
        }
        return $b;
    }

    /**
     * Возвращает `true`, если все шаблонные параметры отделения равны `null`
     *
     * @param Branch $branch
     * @return bool
     */
    public static function areBranchTemplateFieldsEmpty(Branch $branch)
    {
        foreach (self::getTemplateFieldsMap() as $fieldName => $paramName) {
            $getter = 'get' . ucfirst($fieldName);
            if ($branch->$getter() !== null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Выставляет все шаблонные параметры отделения в значения из parameters.yml
     *
     * @param Branch $branch
     */
    public function setDefaultBranchTemplateFields(Branch $branch)
    {
        $defaultBranch = $this->getDefaultBranch();
        foreach (self::getTemplateFieldsMap() as $fieldName => $paramName) {
            $getter = 'get' . ucfirst($fieldName);
            $setter = 'set' . ucfirst($fieldName);
            $branch->$setter($defaultBranch->$getter());
        }
    }
}
