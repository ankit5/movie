<?php

namespace Drupal\domain_simple_sitemap\Controller;

use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\domain\DomainNegotiatorInterface;
use Drupal\simple_sitemap\Controller\SimpleSitemapController;
use Drupal\simple_sitemap\Manager\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\simple_sitemap\Manager\Generator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DomainSimpleSitemapController.
 *
 * @package Drupal\simple_sitemap\Controller
 */
class DomainSimpleSitemapController extends SimpleSitemapController {

  /**
   * Drupal `simple_sitemap.generator` service.
   *
   * @var \Drupal\simple_sitemap\Manager\Generator
   */
  protected $generator;

  /**
   * Drupal `domain.negotiator` service.
   *
   * @var \Drupal\domain\DomainNegotiatorInterface
   */
  protected $domainNegotiator;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * DomainSitemapController constructor.
   *
   * @param \Drupal\simple_sitemap\Manager\Generator $generator
   *   The simple_sitemap.generator service.
   * @param \Drupal\domain\DomainNegotiatorInterface $domain_negoriator
   *   Drupal `domain.negotiator` service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(Generator $generator, DomainNegotiatorInterface $domain_negoriator, EntityTypeManagerInterface $entityTypeManager) {
    $this->generator = $generator;
    $this->domainNegotiator = $domain_negoriator;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): SimpleSitemapController {
    return new static(
      $container->get('simple_sitemap.generator'),
      $container->get('domain.negotiator'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * If variant NULL, default to the domain matching variant.
   *
   * {@inheritdoc}
   */
  public function getSitemap(Request $request, ?string $variant = NULL): Response {
    if (empty($variant)) {
      $active_domain = $this->domainNegotiator->getActiveId();

      if (!empty($active_domain)) {
        $sitemap_types = $this->entityTypeManager->getStorage('simple_sitemap_type')->loadMultiple();

        foreach ($sitemap_types as $st_id => $sitemap_type) {
          $st_domain = $sitemap_type->getThirdPartySetting('domain_simple_sitemap', 'sitemap_domain');
          if ($st_domain === $active_domain) {
            $sitemap_variants = $this->entityTypeManager->getStorage('simple_sitemap')->loadByProperties(['type' => $st_id]);
            foreach (array_keys($sitemap_variants) as $sitemap_variant) {
              $variant = $sitemap_variant;
            }
          }
        }

      }
    }

    $response = parent::getSitemap($request, $variant);
    if ($response instanceof CacheableResponse) {
      $response->getCacheableMetadata()
        ->addCacheContexts(['url']);
    }
    return $response;
  }

}
