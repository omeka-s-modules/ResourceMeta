<?php
namespace ResourceMeta\Entity;

use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Property;
use Omeka\Entity\ResourceTemplate;
use Omeka\Entity\ResourceTemplateProperty;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"resource_template_id", "resource_template_property_id"}
 *         ),
 *     }
 * )
 */
class ResourceMetaResourceTemplateMetaNames extends AbstractEntity
{
    /**
     * @Id
     * @Column(
     *     type="integer",
     *     options={
     *         "unsigned"=true
     *     }
     * )
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\ResourceTemplate"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $resourceTemplate;

    public function setResourceTemplate(ResourceTemplate $resourceTemplate) : void
    {
        $this->resourceTemplate = $resourceTemplate;
    }

    public function getResourceTemplate() : ResourceTemplate
    {
        return $this->resourceTemplate;
    }

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\ResourceTemplateProperty"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $resourceTemplateProperty;

    public function setResourceTemplateProperty(ResourceTemplateProperty $resourceTemplateProperty) : void
    {
        $this->resourceTemplateProperty = $resourceTemplateProperty;
    }

    public function getResourceTemplateProperty() : ResourceTemplateProperty
    {
        return $this->resourceTemplateProperty;
    }

    /**
     * @Column(
     *     type="json",
     *     nullable=false
     * )
     */
    protected $metaNames;

    public function setMetanames(array $metaNames) : void
    {
        $this->metaNames = $metaNames;
    }

    public function getMetaNames() : array
    {
        return is_array($this->metaNames) ? $this->metaNames : [];
    }
}
