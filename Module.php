<?php
namespace ResourceMeta;

use Omeka\Module\AbstractModule;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include sprintf('%s/config/module.config.php', __DIR__);
    }

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        $acl = $this->getServiceLocator()->get('Omeka\Acl');
        $acl->allow(
            null,
            [
                'ResourceMeta\Controller\Admin\Index',
                'ResourceMeta\Controller\Admin\ResourceTemplate',
            ]
        );
    }

    public function install(ServiceLocatorInterface $services)
    {
        $conn = $services->get('Omeka\Connection');
        $sql = <<<'SQL'
CREATE TABLE resource_meta_meta_names (id INT UNSIGNED AUTO_INCREMENT NOT NULL, resource_template_id INT NOT NULL, resource_template_property_id INT NOT NULL, meta_names LONGTEXT NOT NULL COMMENT '(DC2Type:json)', INDEX IDX_F233E16D16131EA (resource_template_id), INDEX IDX_F233E16D2A6B767B (resource_template_property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE resource_meta_meta_names ADD CONSTRAINT FK_F233E16D16131EA FOREIGN KEY (resource_template_id) REFERENCES resource_template (id) ON DELETE CASCADE;
ALTER TABLE resource_meta_meta_names ADD CONSTRAINT FK_F233E16D2A6B767B FOREIGN KEY (resource_template_property_id) REFERENCES resource_template_property (id) ON DELETE CASCADE;
SQL;
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec($sql);
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function uninstall(ServiceLocatorInterface $services)
    {
        $conn = $services->get('Omeka\Connection');
        $conn->exec('DROP TABLE IF EXISTS resource_meta_meta_names;');
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
    }
}
