<?php
namespace ResourceMeta\Stdlib;

use Omeka\Entity\EntityInterface;
use Omeka\Entity\ResourceTemplate;
use Omeka\Entity\ResourceTemplateProperty;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
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
     *
     * This will key the meta names by resource template property ID unless
     * $keyByPropertyId is true.
     */
    public function getResourceTemplateMetaNames(int $resourceTemplateId, bool $keyByPropertyId = false) : array
    {
        $resourceTemplateMetaNames = $this->entityManager
            ->getRepository(ResourceMetaResourceTemplateMetaNames::class)
            ->findBy(['resourceTemplate' => $resourceTemplateId]);

        $resourceTemplateMetaNamesArray = [];
        foreach ($resourceTemplateMetaNames as $metaNames) {
            $key = $keyByPropertyId
                ? $metaNames->getResourceTemplateProperty()->getProperty()->getId()
                : $metaNames->getResourceTemplateProperty()->getId();
            $resourceTemplateMetaNamesArray[$key] = $metaNames->getMetaNames();
        }
        return $resourceTemplateMetaNamesArray;
    }

    /**
     * Persist meta names for a specific resource template.
     */
    public function setResourceTemplateMetaNames(int $resourceTemplateId, array $resourceTemplateMetaNames) : void
    {
        $resourceTemplate = $this->getEntity(ResourceTemplate::class, $resourceTemplateId);
        if (!$resourceTemplate) {
            // This resource template does not exist.
            return;
        }
        // We must set a nonexistent ID (0) or no existing inverses will be
        // deleted if the user unsets all inverse properties in the UI.
        $retainIds = [0];
        foreach ($resourceTemplateMetaNames as $resourceTemplatePropertyId => $metaNamesArray) {
            if (!(is_numeric($resourceTemplatePropertyId) && is_array($metaNamesArray))) {
                // Invalid format.
                continue;
            }
            $resourceTemplateProperty = $this->getEntity(ResourceTemplateProperty::class, $resourceTemplatePropertyId);
            if (!$resourceTemplateProperty) {
                // This resource template property does not exist.
                continue;
            }

            // Prepare the meta names.
            $metaNamesArray = array_filter($metaNamesArray, 'is_string');
            $metaNamesArray = array_map('trim', $metaNamesArray);
            $metaNamesArray = array_unique($metaNamesArray);
            $metaNamesArray = array_filter($metaNamesArray);

            $metaNames = $this->entityManager
                ->getRepository(ResourceMetaResourceTemplateMetaNames::class)
                ->findOneBy(['resourceTemplateProperty' => $resourceTemplateProperty]);
            if ($metaNames) {
                // This entity already exists.
                $metaNames->setMetaNames($metaNamesArray);
            } else {
                // This entity does not exist. Create it.
                $metaNames = new ResourceMetaResourceTemplateMetaNames;
                $metaNames->setResourceTemplate($resourceTemplate);
                $metaNames->setResourceTemplateProperty($resourceTemplateProperty);
                $metaNames->setMetaNames($metaNamesArray);
                $this->entityManager->persist($metaNames);
            }

            // Must flush here so Doctrine generates the ID.
            $this->entityManager->flush();
            $retainIds[] = $metaNames->getId();
        }
        // Delete all meta names that did not already exist and weren't newly
        // created above.
        $dql = 'DELETE FROM ResourceMeta\Entity\ResourceMetaResourceTemplateMetaNames rtmn
        WHERE rtmn.resourceTemplate = :resourceTemplate
        AND rtmn.id NOT IN (:ids)';
        $this->entityManager
            ->createQuery($dql)
            ->setParameter('resourceTemplate', $resourceTemplate)
            ->setParameter('ids', $retainIds)
            ->execute();
    }

    /**
     * Add meta tags to a resource page.
     */
    public function addResourceMeta(PhpRenderer $view) : void
    {
        $resource = $view->resource;

        $resourceTemplate = $resource->resourceTemplate();
        if (!$resourceTemplate) {
            // This resource has no resource template.
            return;
        }
        // Note that we key meta names by property ID.
        $resourceTemplateMetaNames = $this->getResourceTemplateMetaNames($resourceTemplate->id(), true);
        foreach ($resource->values() as $propertyValues) {
            $propertyId = $propertyValues['property']->id();
            if (!array_key_exists($propertyId, $resourceTemplateMetaNames)) {
                // This property has no meta names.
                continue;
            }
            // Iterate the values. Get the meta content.
            foreach ($propertyValues['values'] as $value) {
                $metaContent = null;
                if ($value->valueResource()) {
                    $metaContent = $value->__toString();
                } elseif ($value->uri()) {
                    $metaContent = $value->uri();
                } elseif ($value->value()) {
                    $metaContent = $value->value();
                }
                if (!$metaContent) {
                    // This value has no content.
                    continue;
                }
                // Iterate the meta names. Set the meta tags.
                foreach ($resourceTemplateMetaNames[$propertyId] as $metaName) {
                    $view->headMeta()->appendName($metaName, $metaContent);
                }
            }
        }
    }
}
