<?php

namespace Drupal\domain_simple_sitemap\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Alter the default `simple_sitemap.sitemap_default` route.
 *
 * Override the controller to serve domain specific variants at:
 * - https://example.com/sitemap.xml.
 * - https://domain.example.com/sitemap.xml.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
      if ($route = $collection->get('simple_sitemap.sitemap_default')) {
          $route->setDefault('_controller', '\Drupal\domain_simple_sitemap\Controller\DomainSimpleSitemapController::getSitemap');
      }
  }

}
