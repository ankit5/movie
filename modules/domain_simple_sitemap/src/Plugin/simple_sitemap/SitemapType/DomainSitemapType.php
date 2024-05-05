<?php

namespace Drupal\feature_domain\Plugin\simple_sitemap\SitemapType;

/**
 * The domain sitemap type.
 *
 * @SitemapType(
 *   id = "domain",
 *   label = @Translation("Domain"),
 *   description = @Translation("The domain sitemap type."),
 *   sitemapGenerator = "default",
 *   urlGenerators = {
 *     "domain_entity"
 *   },
 * )
 */
class DomainSitemapType extends SitemapTypeBase {
}
