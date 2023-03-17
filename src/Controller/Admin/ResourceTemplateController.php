<?php
namespace ResourceMeta\Controller\Admin;

use Laminas\Form\Form;
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
    }
}
