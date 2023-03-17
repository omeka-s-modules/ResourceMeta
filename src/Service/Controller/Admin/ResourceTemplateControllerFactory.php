<?php
namespace ResourceMeta\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use ResourceMeta\Controller\Admin\ResourceTemplateController;
use Zend\ServiceManager\Factory\FactoryInterface;

class ResourceTemplateControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $metaNames = $services->get('Config')['resource_meta_meta_names'];
        $entityManager = $services->get('Omeka\EntityManager');
        return new ResourceTemplateController($metaNames, $entityManager);
    }
}
