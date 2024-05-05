<?php

namespace Drupal\domain_simple_sitemap\Plugin\simple_sitemap\UrlGenerator;

use Drupal\Core\Url;
use Drupal\domain_access\DomainAccessManagerInterface;
use Drupal\simple_sitemap\Plugin\simple_sitemap\UrlGenerator\EntityUrlGenerator;

/**
 * Generates URLs for entity bundles and bundle overrides.
 *
 * @UrlGenerator(
 *   id = "domain_entity",
 *   label = @Translation("Domain entity URL generator"),
 *   description = @Translation("Generates URLs for entity bundles and bundle
 *   overrides."),
 * )
 */
class DomainEntityUrlGenerator extends EntityUrlGenerator {

  /**
   * {@inheritdoc}
   */
  public function getDataSets(): array {
    $data_sets = [];
    $sitemap_entity_types = $this->entityHelper->getSupportedEntityTypes();
    $all_bundle_settings = $this->entitiesManager->setVariants($this->sitemap->id())->getAllBundleSettings();
    $domainStorage = \Drupal::entityTypeManager()->getStorage('domain');
    $original_domain = \Drupal::service('domain.negotiator')->getActiveDomain();

    if (isset($all_bundle_settings[$this->sitemap->id()])) {
      foreach ($all_bundle_settings[$this->sitemap->id()] as $entity_type_name => $bundles) {
        if (!isset($sitemap_entity_types[$entity_type_name])) {
          continue;
        }

        if ($this->isOverwrittenForEntityType($entity_type_name)) {
          continue;
        }

        $entityTypeStorage = $this->entityTypeManager->getStorage($entity_type_name);
        $keys = $sitemap_entity_types[$entity_type_name]->getKeys();

        foreach ($bundles as $bundle_name => $bundle_settings) {
          if ($bundle_settings['index']) {
            $query = $entityTypeStorage->getQuery()->accessCheck(FALSE);

            if (!empty($keys['id'])) {
              $query->sort($keys['id']);
            }
            if (!empty($keys['bundle'])) {
              $query->condition($keys['bundle'], $bundle_name);
            }
            if (!empty($keys['published'])) {
              $query->condition($keys['published'], 1);
            }
            elseif (!empty($keys['status'])) {
              $query->condition($keys['status'], 1);
            }

            // Get the selected domain for the sitemap type.
            $sitemap_domain_id = $this->sitemap->getType()->getThirdPartySetting('domain_simple_sitemap', 'sitemap_domain');
            if (!$sitemap_domain_id) {
              continue;
            }
            // Activate the domain.
            $domain = $domainStorage->load($sitemap_domain_id);
            \Drupal::service('domain.negotiator')->setActiveDomain($domain);
            // Filter nodes based on their Domain Access settings.
            $domain_entity = FALSE;
            $all_bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_name, $bundle_name);
            if (\Drupal::service('module_handler')->moduleExists('domain_entity')) {
              if (array_key_exists(\Drupal\domain_entity\DomainEntityMapper::FIELD_NAME, $all_bundle_fields)) {
                // Setting this query tag will cause the domain_entity module
                // to alter the query to add domain access conditions.
                $query->addTag($entity_type_name . '_access');
                $domain_entity = TRUE;
              }
            }
            if (!$domain_entity && array_key_exists(DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD, $all_bundle_fields)) {
              $orGroupDomain = $query->orConditionGroup()
                ->condition(DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD, $sitemap_domain_id)
                ->condition(DomainAccessManagerInterface::DOMAIN_ACCESS_ALL_FIELD, 1);
              $query->condition($orGroupDomain);
            }

            // Shift access check to EntityUrlGeneratorBase for language
            // specific access.
            // See https://www.drupal.org/project/simple_sitemap/issues/3102450.
            $query->accessCheck(FALSE);

            $data_set = [
              'entity_type' => $entity_type_name,
              'id' => [],
              'domain' => $sitemap_domain_id,
            ];
            $entities = [];
            foreach ($query->execute() as $entity_id) {
              $entities[] = $entity_id;
              $data_set['id'][] = $entity_id;
              if (count($data_set['id']) >= $this->entitiesPerDataset) {
                $data_sets[] = $data_set;
                $data_set['id'] = [];
              }
            }

            // Add the last data set if there are some IDs gathered.
            if (!empty($data_set['id'])) {
              $data_sets[] = $data_set;
            }
          }
        }
      }
    }
    \Drupal::service('domain.negotiator')->setActiveDomain($original_domain);
    return $data_sets;
  }


  /**
   * {@inheritdoc}
   */
  public function generate($data_set): array {
    $original_domain = \Drupal::service('domain.negotiator')->getActiveDomain();
    // Set active domain to the one matching the dataset.
    if (isset($data_set['domain'])) {
      $domainStorage = \Drupal::entityTypeManager()->getStorage('domain');
      $domain = $domainStorage->load($data_set['domain']);
      \Drupal::service('domain.negotiator')->setActiveDomain($domain);
    }

    $path_data_sets = $this->processDataSet($data_set);
    $url_variant_sets = [];
    foreach ($path_data_sets as $path_data) {
      if (isset($path_data['url']) && $path_data['url'] instanceof Url) {
        $url_object = $path_data['url'];
        unset($path_data['url']);
        $url_variant_sets[] = $this->getUrlVariants($path_data, $url_object);
      }
    }

    // Make sure to clear entity memory cache so it does not build up resulting
    // in a constant increase of memory.
    // See https://www.drupal.org/project/simple_sitemap/issues/3170261 and
    // https://www.drupal.org/project/simple_sitemap/issues/3202233
    if ($this->entityTypeManager->getDefinition($data_set['entity_type'])->isStaticallyCacheable()) {
      $this->entityMemoryCache->deleteAll();
    }

    \Drupal::service('domain.negotiator')->setActiveDomain($original_domain);
    return array_merge([], ...$url_variant_sets);
  }

  /**
   * To replace base URL by custom one.
   *
   * @param string $url
   *   The custom URL.
   *
   * @return string
   *   A replaced URL.
   */
  protected function replaceBaseUrlWithCustom(string $url): string {
    /** @var \Drupal\domain\DomainInterface $domain */
    $domain = \Drupal::service('domain.negotiator')->getActiveDomain();
    $url_parts = explode("/", $url);
    /*
     * Add a check for the country path module
     * @see https://www.drupal.org/project/domain_simple_sitemap/issues/2922447
     * @see https://www.drupal.org/project/simple_sitemap/issues/3025960
     */
    $domain_suffix = $domain->getThirdPartySetting('country_path', 'domain_path');
    if ($domain_suffix !== NULL) {
      // Need to look at all domains and check each suffix.
      // If this process (generation) was triggered under one of
      // the domains with a country path suffix then we are dealing
      // with $url in the format $baseurl/$current_suffix/$url and need to
      // remove $current_suffix.
      $domain_storage = \Drupal::entityTypeManager()->getStorage('domain');
      $all_domains = $domain_storage->loadMultipleSorted();
      foreach ($all_domains as $current_domain) {
        $cur_suffix = $current_domain->getThirdPartySetting('country_path', 'domain_path');
        if ($cur_suffix != '' && in_array($cur_suffix, $url_parts)) {
          $url = str_replace("/" . $cur_suffix . "/", "/", $url);
          break;
        }
      }
      $custom_url = str_replace($GLOBALS['base_url'], $domain->getScheme() . $domain->getCanonical() . '/' . $domain_suffix, $url);
    }
    else {
      $custom_url = str_replace($GLOBALS['base_url'], $domain->getScheme() . $domain->getCanonical(), $url);
    }
    return $custom_url;
  }

}
