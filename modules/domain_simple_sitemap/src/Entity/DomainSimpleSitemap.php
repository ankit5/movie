<?php

namespace Drupal\domain_simple_sitemap\Entity;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;
use Drupal\simple_sitemap\Entity\SimpleSitemap;


class DomainSimpleSitemap extends SimpleSitemap {

  /**
   * {@inheritdoc}
   */
  public function toUrl($rel = 'canonical', array $options = []) {
    if ($rel !== 'canonical') {
      return parent::toUrl($rel, $options);
    }

    $parameters = isset($options['delta']) ? ['page' => $options['delta']] : [];
    unset($options['delta']);

    if (empty($options['base_url'])) {
      /** @var \Drupal\simple_sitemap\Settings $settings */
      $settings = \Drupal::service('simple_sitemap.settings');
      $options['base_url'] = $settings->get('base_url') ?: $GLOBALS['base_url'];

      $storage = \Drupal::entityTypeManager()->getStorage('domain');
      $domains = $storage->loadMultiple();
      $sitemap_type = $this->get('type');

      // Try to get domain's path.
      foreach ($domains as $key => $domain) {
        if ($key == $sitemap_type) {
          $options['base_url'] = rtrim($domain->getPath(), '/');
        }
      }
    }
    $options['language'] = $this->languageManager()->getLanguage(LanguageInterface::LANGCODE_NOT_APPLICABLE);

    return $this->isDefault()
      ? Url::fromRoute(
        'simple_sitemap.sitemap_default',
        $parameters,
        $options)
      : Url::fromRoute(
        'simple_sitemap.sitemap_variant',
        $parameters + ['variant' => $this->id()],
        $options);
  }

}
