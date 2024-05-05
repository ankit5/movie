<?php

namespace Drupal\domain_simple_sitemap;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\domain\DomainInterface;
use Drupal\domain\DomainValidatorInterface;
use Drupal\simple_sitemap\Entity\SimpleSitemapType;

/**
 * The service for domain_simple_sitemap module.
 *
 * @package Drupal\domain_simple_sitemap
 */
class DomainSitemapManager {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The domain validator.
   *
   * @var \Drupal\domain\DomainValidatorInterface
   */
  protected $domainValidator;

  /**
   * DomainSitemapManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\domain\DomainValidatorInterface $domainValidator
   *   The domain validator service.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    DomainValidatorInterface $domainValidator
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->domainValidator = $domainValidator;
  }

  /**
   * Add sitemap variant for domain.
   *
   * @param \Drupal\domain\DomainInterface $domain
   *   The domain to validate for syntax and uniqueness.
   */
  public function addSitemapVariant(DomainInterface $domain) {
    // Test if it does not already exist.
    $sitemap_type = SimpleSitemapType::load($domain->id());
    if (!is_null($sitemap_type)) {
      \Drupal::logger('domain_simple_sitemap')->notice('Failed creating new sitemap type because it already exists: ' . $domain->id());
      return;
    }
    // Create a new sitemap type for the domain.
    $type_storage = \Drupal::entityTypeManager()->getStorage('simple_sitemap_type');
    if ($type_storage->load($domain->id()) === NULL) {
      $domain_sitemap = $type_storage->create([
        'id' => $domain->id(),
        'label' => $domain->label() . ' sitemap',
        'description' => 'Sitemap type for domain: ' . $domain->label(),
        'sitemap_generator' => 'default',
        'url_generators' => [
          'domain_entity',
          'custom',
          'entity',
          'entity_menu_link_content',
          'arbitrary',
        ],
      ]);
      // Set the domain to the sitemap type via third party settings.
      $domain_sitemap->setThirdPartySetting('domain_simple_sitemap', 'sitemap_domain', $domain->id());
      // Save our new sitemap type.
      $domain_sitemap->save();
    }

    // Generate the site map.
    /** @var \Drupal\simple_sitemap\Manager\Generator $generator */
    $generator = \Drupal::service('simple_sitemap.generator');
    $generator
      ->rebuildQueue()
      ->generate();
  }

  /**
   * Delete sitemap variant for domain.
   *
   * @param \Drupal\domain\DomainInterface $domain
   *   The domain of which to delete the sitemap type from.
   */
  public function deleteSitemapVariant(DomainInterface $domain) {
    $sitemap_type = SimpleSitemapType::load($domain->id());
    if ($sitemap_type) {
      $sitemap_type->delete();
    }
  }

}
