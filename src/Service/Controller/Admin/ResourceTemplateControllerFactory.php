<?php
namespace ResourceMeta\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use ResourceMeta\Controller\Admin\ResourceTemplateController;
use Zend\ServiceManager\Factory\FactoryInterface;

class ResourceTemplateControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ResourceTemplateController($services->get('ResourceMeta\ResourceMeta'));
    }
}
