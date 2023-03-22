<?php
namespace ResourceMeta\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Laminas\Form;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ResourceTemplateController extends AbstractActionController
{
    protected $resourceMeta;

    public function __construct($resourceMeta)
    {
        $this->resourceMeta = $resourceMeta;
    }

    public function browseAction()
    {
        $resourceTemplates = $this->api()->search('resource_templates', ['sort_by' => 'label'])->getContent();

        $resourceTemplatesMetaNames = [];
        foreach ($resourceTemplates as $resourceTemplate) {
            $resourceTemplateMetaNames = $this->resourceMeta->getResourceTemplateMetaNames($resourceTemplate->id());
            $resourceTemplatesMetaNames[$resourceTemplate->id()] = array_merge(...$resourceTemplateMetaNames);
        }

        $view = new ViewModel;
        $view->setVariable('resourceTemplates', $resourceTemplates);
        $view->setVariable('resourceTemplatesMetaNames', $resourceTemplatesMetaNames);
        return $view;
    }

    public function showAction()
    {
        $resourceTemplate = $this->resourceMeta->getEntity('Omeka\Entity\ResourceTemplate', $this->params('id'));
        $resourceTemplateMetaNames = $this->resourceMeta->getResourceTemplateMetaNames($resourceTemplate->getId());

        $view = new ViewModel;
        $view->setVariable('resourceTemplate', $resourceTemplate);
        $view->setVariable('resourceTemplateMetaNames', $resourceTemplateMetaNames);
        return $view;
    }

    public function editAction()
    {
        $resourceTemplate = $this->resourceMeta->getEntity('Omeka\Entity\ResourceTemplate', $this->params('id'));
        $resourceTemplateMetaNames = $this->resourceMeta->getResourceTemplateMetaNames($resourceTemplate->getId());

        if (!$this->userIsAllowed($resourceTemplate, 'update')) {
            return $this->redirect()->toRoute('admin/resource-meta', [], true);
        }

        // Must use a generic form for CSRF protection.
        $form = $this->getForm(Form\Form::class);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $resourceTemplateMetaNames = $this->params()->fromPost('resource_template_meta_names', []);
                $this->resourceMeta->setResourceTemplateMetaNames($resourceTemplate->getId(), $resourceTemplateMetaNames);
                $this->messenger()->addSuccess('Resource meta successfully updated'); // @translate
                return $this->redirect()->toRoute(null, ['action' => 'show'], true);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        // Build the multiselect.
        $valueOptions = [];
        foreach ($this->resourceMeta->getMetaNames() as $optgroupName => $optgroupData) {
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
            'class' => 'meta-name-select chosen-select',
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
