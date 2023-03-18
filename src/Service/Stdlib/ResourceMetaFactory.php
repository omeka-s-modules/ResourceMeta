<?php
namespace ResourceMeta\Service\Stdlib;

use Interop\Container\ContainerInterface;
use ResourceMeta\Stdlib\ResourceMeta;
use Zend\ServiceManager\Factory\FactoryInterface;

class ResourceMetaFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new ResourceMeta($services);
    }
}
