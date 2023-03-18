<?php
namespace ResourceMeta\Stdlib;

use Omeka\Entity\ResourceTemplate;
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
     *
     * @return array
     */
    public function getMetaNames()
    {
        return $this->metaNames;
    }

    /**
     * Get a resource template entity.
     *
     * @param int $resourceTemplateId
     * @return ResourceTemplate
     */
    public function getResourceTemplate($resourceTemplateId)
    {
        return $this->entityManager->find('Omeka\Entity\ResourceTemplate', $resourceTemplateId);
    }

    /**
     * Get persisted meta names for a specific resource template.
     *
     * @param ResourceTemplate $resourceTemplate
     * @return array Keyed by resource template property ID
     */
    public function getResourceTemplateMetaNames(ResourceTemplate $resourceTemplate)
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
    public function setResourceTemplateMetaNames(array $resourceTemplateMetaNames)
    {
        echo '<pre>';print_r($resourceTemplateMetaNames);exit;
        // @todo Persist meta names
    }
}
