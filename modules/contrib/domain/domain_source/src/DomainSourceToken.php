<?php

namespace Drupal\domain_source;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * Token handler for Domain Source.
 *
 * TokenAPI still uses procedural code, but we have moved it to a class for
 * easier refactoring.
 */
class DomainSourceToken {

  use StringTranslationTrait;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * An array of routes to ignore.
   *
   * @var array
   */
  public $excludedRoutes;

  /**
   * Constructs a DomainSourceToken object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Implements hook_token_info().
   */
  public function getTokenInfo() {
    // Domain Source tokens.
    $info = [
      'tokens' => [
        'node' => [
          'canonical-source-domain-url' => [
            'name' => $this->t('Canonical Source Domain URL'),
            'description' => $this->t("The canonical URL from the source domain for this node."),
            'type' => 'node',
          ],
        ],
      ],
    ];

    return $info;
  }

  /**
   * Implements hook_tokens().
   */
  public function getTokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
    $replacements = [];

    // Based on the type, get the proper domain context.
    switch ($type) {
      case 'node':
        foreach ($tokens as $name => $original) {
          if ($name !== 'canonical-source-domain-url') {
            continue;
          }
          if (isset($data['node']) && $data['node'] instanceof NodeInterface) {
            $node = $data['node'];
            $original = $tokens['canonical-source-domain-url'];
            // @phpstan-ignore-next-line
            if (in_array('canonical', $this->getExcludedRoutes(), TRUE) && $node->hasField('field_domain_source') && !$node->field_domain_source->isEmpty()) {
              /** @var \Drupal\domain\Entity\Domain $sourceDomain */
              // @phpstan-ignore-next-line
              $sourceDomain = $node->field_domain_source->entity;
              $url = $node->toUrl('canonical')->toString();
              $replacements[$original] = $sourceDomain->buildUrl($url);
              $bubbleable_metadata->addCacheableDependency($sourceDomain);
            }
            else {
              $replacements[$original] = $node->toUrl('canonical')->setAbsolute()->toString();
            }
          }
        }
        break;
    }

    return $replacements;
  }

  /**
   * Gets the settings for domain source path rewrites.
   *
   * @return array
   *   The settings for domain source path rewrites.
   */
  public function getExcludedRoutes() {
    if (!isset($this->excludedRoutes)) {
      $config = $this->configFactory->get('domain_source.settings');
      $routes = $config->get('exclude_routes');
      if (is_array($routes)) {
        $this->excludedRoutes = array_flip($routes);
      }
      else {
        $this->excludedRoutes = [];
      }
    }
    return $this->excludedRoutes;
  }

}
