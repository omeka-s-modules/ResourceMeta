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

        $view = new ViewModel;
        $view->setVariable('resourceTemplates', $resourceTemplates);
        return $view;
    }

    public function showAction()
    {
        $resourceTemplate = $this->resourceMeta->getResourceTemplate($this->params('id'));

        $view = new ViewModel;
        $view->setVariable('resourceTemplate', $resourceTemplate);
        $view->setVariable('resourceTemplateMetaNames', $this->resourceMeta->getResourceTemplateMetaNames($resourceTemplate));
        return $view;
    }

    public function editAction()
    {
        $resourceTemplate = $this->resourceMeta->getResourceTemplate($this->params('id'));
        if (!$this->userIsAllowed($resourceTemplate, 'update')) {
            return $this->redirect()->toRoute('admin/resource-meta', [], true);
        }

        // Must use a generic form for CSRF protection.
        $form = $this->getForm(Form\Form::class);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $this->resourceMeta->setResourceTemplateMetaNames($this->params()->fromPost('resource_template_meta_names', []));
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
            'class' => 'chosen-select',
            'data-placeholder' => 'Select meta names…', // @translate
        ]);

        $view = new ViewModel;
        $view->setVariable('form', $form);
        $view->setVariable('select', $select);
        $view->setVariable('resourceTemplate', $resourceTemplate);
        $view->setVariable('resourceTemplateMetaNames', $this->resourceMeta->getResourceTemplateMetaNames($resourceTemplate));
        return $view;
    }
}
