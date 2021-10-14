<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Branch;
use AppBundle\Service\BranchService;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class BranchAdmin extends BaseAdmin
{
    /**
     * @var BranchService
     */
    private $branchService;

    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'name',
    );

    protected $translationDomain = 'AppBundle';

    public function __construct($code, $class, $baseControllerName, $branchService)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->branchService = $branchService;
    }

    /**
     * При создании нового отделения подгружаем дефолтные параметры.
     *
     * @return Branch
     */
    public function getNewInstance()
    {
        $new = $this->branchService->getDefaultBranch();
        $new->setName('');
        return $new;
    }

    /**
     * Переопределённый метод.
     *
     * Если объект найден и понятно, что у него не заполнено ни одного шаблонного параметра,
     * они дозаполняются значениями из файла parameters.yml
     *
     * Это удобно во время переходного периода, когда отделения уже созданы в БД, но все параметры пока пустые.
     * При рендере справок и документов в этом случае будут использованы параметры из файла,
     * и чтобы на странице редактирования не нужно было копировать их вручную, они дозаполняются автоматически тут.
     * Также это полезно, т.к. у человека, который будет редактировать поля, будут референсные значения под рукой.
     *
     * @param mixed $id
     * @return mixed|object
     */
    public function getObject($id)
    {
        $existingObject = parent::getObject($id);
        if ($existingObject !== null && BranchService::areBranchTemplateFieldsEmpty($existingObject)) {
            $this->branchService->setDefaultBranchTemplateFields($existingObject);
        }
        return $existingObject;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ]);

        $formMapper->end()->with('template_fields', [
            'label' => 'Параметры для шаблонов',
            'description' => 'Параметры для шаблонов справок и документов.',
        ]);

        foreach (BranchService::getTemplateFieldsMap() as $fieldName => $paramName) {
            $type = TextType::class;
            $options = ['label' => $paramName, 'required' => false];
            if ($paramName == 'org_contacts_full') {
                $type = TextareaType::class;
                $options['attr'] = ['rows' => 6];
            }
            $formMapper->add($fieldName, $type, $options);
        }
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->addIdentifier('name', null, [
                'label' => 'Название',
            ])
            ->add('shortName', null, [
                'label' => 'Сокращение',
            ])
            ->add('_action', null, [
                'label' => 'Действие',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

}
