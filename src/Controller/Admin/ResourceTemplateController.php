<?php
namespace ResourceMeta\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Laminas\Form;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Omeka\Entity\ResourceTemplate;

class ResourceTemplateController extends AbstractActionController
{
    protected $metaNames;
    protected $entityManager;

    public function __construct(array $metaNames, EntityManager $entityManager)
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
        $resourceTemplate = $this->getResourceTemplate($this->params('id'));

        $view = new ViewModel;
        $view->setVariable('resourceTemplate', $resourceTemplate);
        $view->setVariable('resourceTemplateMetaNames', $this->getResourceTemplateMetaNames($resourceTemplate));
        return $view;
    }

    public function editAction()
    {
        $resourceTemplate = $this->getResourceTemplate($this->params('id'));
        if (!$this->userIsAllowed($resourceTemplate, 'update')) {
            return $this->redirect()->toRoute('admin/resource-meta', [], true);
        }

        // Must use a generic form for CSRF protection.
        $form = $this->getForm(Form\Form::class);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $this->setResourceTemplateMetaNames($this->params()->fromPost('resource_template_meta_names', []));
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
        $view->setVariable('resourceTemplateMetaNames', $this->getResourceTemplateMetaNames($resourceTemplate));
        return $view;
    }

    /**
     * Get a resource template entity.
     *
     * @param int $resourceTemplateId
     * @return ResourceTemplate
     */
    protected function getResourceTemplate($resourceTemplateId)
    {
        return $this->entityManager->find('Omeka\Entity\ResourceTemplate', $resourceTemplateId);
    }

    /**
     * Get persisted meta names for a specific resource template.
     *
     * @param ResourceTemplate $resourceTemplate
     * @return array Keyed by resource template property ID
     */
    protected function getResourceTemplateMetaNames(ResourceTemplate $resourceTemplate)
    {
        $resourceTemplateMetaNamesEntities = $this->entityManager
            ->getRepository('ResourceMeta\Entity\ResourceMetaResourceTemplateMetaNames')
            ->findBy(['resourceTemplate' => $resourceTemplate]);
        $resourceTemplateMetaNames = [];
        foreach ($resourceTemplateMetaNamesEntities as $resourceTemplateMetaNamesEntity) {
            $resourceTemplateMetaNames[$resourceTemplateMetaNamesEntity->getResourceTemplateProperty()->getId()] = $resourceTemplateMetaNamesEntity->getMetaNames();
        }
        return $resourceTemplateMetaNames;
    }

    /**
     * Persist meta names for a specific resource template.
     *
     * @param array $resourceTemplateMetaNames
     */
    protected function setResourceTemplateMetaNames(array $resourceTemplateMetaNames)
    {
        echo '<pre>';print_r($resourceTemplateMetaNames);exit;
        // @todo Persist meta names
    }
}
