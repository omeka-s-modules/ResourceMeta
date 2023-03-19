<?php
namespace ResourceMeta\Stdlib;

use Omeka\Entity;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ResourceMeta
{
    protected $services;

    protected $metaNames;

    protected $entityManager;

    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
        $this->metaNames = $services->get('Config')['resource_meta_meta_names'];
        $this->entityManager = $this->services->get('Omeka\EntityManager');
    }

    /**
     * Get meta names from config.
     */
    public function getMetaNames() : array
    {
        return $this->metaNames;
    }

    /**
     * Get an entity.
     */
    public function getEntity(string $entityName, int $entityId) : ?Entity\EntityInterface
    {
        return $this->entityManager->find($entityName, $entityId);
    }

    /**
     * Get persisted meta names for a specific resource template.
     */
    public function getResourceTemplateMetaNames(int $resourceTemplateId) : array
    {
        $resourceTemplateMetaNamesEntities = $this->entityManager
            ->getRepository('ResourceMeta\Entity\ResourceMetaResourceTemplateMetaNames')
            ->findBy(['resourceTemplate' => $resourceTemplateId]);
        $resourceTemplateMetaNames = [];
        foreach ($resourceTemplateMetaNamesEntities as $resourceTemplateMetaNamesEntity) {
            $resourceTemplatePropertyId = $resourceTemplateMetaNamesEntity->getResourceTemplateProperty()->getId();
            $resourceTemplateMetaNames[$resourceTemplatePropertyId] = $resourceTemplateMetaNamesEntity->getMetaNames();
        }
        return $resourceTemplateMetaNames;
    }

    /**
     * Persist meta names for a specific resource template.
     */
    public function setResourceTemplateMetaNames(array $resourceTemplateMetaNames) : void
    {
        echo '<pre>';print_r($resourceTemplateMetaNames);exit;
        // @todo Persist meta names
    }
}
