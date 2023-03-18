<?php
namespace ResourceMeta\Controller\Admin;

use Laminas\Form;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ResourceTemplateController extends AbstractActionController
{
    protected $metaNames;
    protected $entityManager;

    public function __construct(array $metaNames, $entityManager)
    {
        $this->metaNames = $metaNames;
        $this->entityManager = $entityManager;
    }

    public function browseAction()
    {
        $resourceTemplates = $this->api()->search('resource_templates', ['sort_by' => 'label'])->getContent();

        $view = new ViewModel;
        $view->setVariable('resourceTemplates', $resourceTemplates);
        return $view;
    }

    public function showAction()
    {
        $resourceTemplateId = $this->params('id');
        $resourceTemplate = $this->entityManager->find('Omeka\Entity\ResourceTemplate', $resourceTemplateId);

        $view = new ViewModel;
        $view->setVariable('resourceTemplate', $resourceTemplate);
        return $view;
    }

    public function editAction()
    {
        $resourceTemplateId = $this->params('id');
        $resourceTemplate = $this->entityManager->find('Omeka\Entity\ResourceTemplate', $resourceTemplateId);
        if (!$this->userIsAllowed($resourceTemplate, 'update')) {
            return $this->redirect()->toRoute('admin/resource-meta', [], true);
        }

        $resourceTemplateMetaNamesEntities = $this->entityManager
            ->getRepository('ResourceMeta\Entity\ResourceMetaResourceTemplateMetaNames')
            ->findBy(['resourceTemplate' => $resourceTemplate]);
        $resourceTemplateMetaNames = [];
        foreach ($resourceTemplateMetaNamesEntities as $resourceTemplateMetaNamesEntity) {
            $resourceTemplateMetaNames[$resourceTemplateMetaNamesEntity->getResourceTemplateProperty()->getId()] = $resourceTemplateMetaNamesEntity->getMeta();
        }

        // Must use a generic form for CSRF protection.
        $form = $this->getForm(Form\Form::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                echo '<pre>';print_r($this->params()->fromPost());exit;
                // @todo Save resource meta via $this->params()->fromPost('resource_meta')
                // - delete all rows for this resource template
                // - add user selected rows
                $this->messenger()->addSuccess('Resource meta successfully updated'); // @translate
                return $this->redirect()->toRoute(null, ['action' => 'show'], true);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        // Build the multiselect.
        $valueOptions = [];
        foreach ($this->metaNames as $optgroupName => $optgroupData) {
            $valueOptions[$optgroupName] = [
                'label' => $optgroupData['label'],
                'options' => [],
            ];
            foreach ($optgroupData['meta_names'] as $metaName) {
                $valueOptions[$optgroupName]['options'][$metaName] = $metaName;
            }
        }
        $select = new Form\Element\Select('meta_name');
        $select->setValueOptions($valueOptions);
        $select->setAttributes([
            'multiple' => true,
            'class' => 'chosen-select',
            'data-placeholder' => 'Select meta names…', // @translate
        ]);

        $view = new ViewModel;
        $view->setVariable('form', $form);
        $view->setVariable('select', $select);
        $view->setVariable('resourceTemplate', $resourceTemplate);
        $view->setVariable('resourceTemplateMetaNames', $resourceTemplateMetaNames);
        return $view;
    }
}
