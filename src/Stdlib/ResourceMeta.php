<?php
namespace ResourceMeta\Stdlib;

use Omeka\Entity\EntityInterface;
use Omeka\Entity\ResourceTemplate;
use Omeka\Entity\ResourceTemplateProperty;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ResourceMeta\Entity\ResourceMetaResourceTemplateMetaNames;

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
    public function getEntity(string $entityName, int $entityId) : ?EntityInterface
    {
        return $this->entityManager->find($entityName, $entityId);
    }

    /**
     * Get persisted meta names for a specific resource template.
     */
    public function getResourceTemplateMetaNames(int $resourceTemplateId) : array
    {
        $resourceTemplateMetaNamesEntities = $this->entityManager
            ->getRepository(ResourceMetaResourceTemplateMetaNames::class)
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
    public function setResourceTemplateMetaNames(int $resourceTemplateId, array $resourceTemplateMetaNames) : void
    {
        // echo '<pre>';print_r($resourceTemplateMetaNames);exit;
        $resourceTemplateEntity = $this->getEntity(ResourceTemplate::class, $resourceTemplateId);
        if (!$resourceTemplateEntity) {
            // This resource template does not exist.
            return;
        }
        // We must set a nonexistent ID (0) or no existing inverses will be
        // deleted if the user unsets all inverse properties in the UI.
        $retainIds = [0];
        foreach ($resourceTemplateMetaNames as $resourceTemplatePropertyId => $metaNames) {
            if (!(is_numeric($resourceTemplatePropertyId) && is_array($metaNames))) {
                // Invalid format.
                continue;
            }
            $resourceTemplatePropertyEntity = $this->getEntity(ResourceTemplateProperty::class, $resourceTemplatePropertyId);
            if (!$resourceTemplatePropertyEntity) {
                // This resource template property does not exist.
                continue;
            }
            // Prepare the meta names.
            $metaNames = array_filter($metaNames, 'is_string');
            $metaNames = array_map('trim', $metaNames);
            $metaNames = array_unique($metaNames);
            $metaNames = array_filter($metaNames);
            $metaNamesEntity = $this->entityManager
                ->getRepository(ResourceMetaResourceTemplateMetaNames::class)
                ->findOneBy(['resourceTemplateProperty' => $resourceTemplatePropertyEntity]);
            if ($metaNamesEntity) {
                // This entity already exists.
                $metaNamesEntity->setMetaNames($metaNames);
            } else {
                // This entity does not exist. Create it.
                $metaNamesEntity = new ResourceMetaResourceTemplateMetaNames;
                $metaNamesEntity->setResourceTemplate($resourceTemplateEntity);
                $metaNamesEntity->setResourceTemplateProperty($resourceTemplatePropertyEntity);
                $metaNamesEntity->setMetaNames($metaNames);
                $this->entityManager->persist($metaNamesEntity);
            }
            // Must flush here so Doctrine generates the ID.
            $this->entityManager->flush();
            $retainIds[] = $metaNamesEntity->getId();
        }
        // Delete all meta names that did not already exist and weren't newly
        // created above.
        $dql = 'DELETE FROM ResourceMeta\Entity\ResourceMetaResourceTemplateMetaNames rtmn
        WHERE rtmn.resourceTemplate = :resourceTemplate
        AND rtmn.id NOT IN (:ids)';
        $this->entityManager
            ->createQuery($dql)
            ->setParameter('resourceTemplate', $resourceTemplateEntity)
            ->setParameter('ids', $retainIds)
            ->execute();
    }
}
