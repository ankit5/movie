<?php

namespace Drupal\Tests\domain_source\Functional;

use Drupal\domain_access\DomainAccessManagerInterface;
use Drupal\domain_source\DomainSourceElementManagerInterface;
use Drupal\Tests\domain\Functional\DomainTestBase;

/**
 * Tests behavior for getting all URLs for an entity.
 *
 * @group domain_source
 */
class DomainSourceTokenTest extends DomainTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain',
    'domain_access',
    'domain_source',
    'field',
    'node',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create 4 domains.
    $this->domainCreateTestDomains(4, 'example.com');
  }

  /**
   * Tests domain source tokens.
   */
  public function testDomainSourceTokens() {
    $token_handler = \Drupal::token();
    /** @var \Drupal\domain\DomainInterface[] $domains */
    $domains = \Drupal::entityTypeManager()->getStorage('domain')->loadMultiple();
    // Create a node, assigned to a source domain.
    $nodes_values = [
      'type' => 'page',
      'title' => 'foo',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [
        'example_com',
        'one_example_com',
        'two_example_com',
      ],
      DomainAccessManagerInterface::DOMAIN_ACCESS_ALL_FIELD => 0,
      DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD => 'one_example_com',
    ];
    $node = $this->createNode($nodes_values);

    // Token value matches the normal canonical url for canonical rewrites.
    $this->assertEquals($token_handler->replace('[node:canonical-source-domain-url]', ['node' => $node]), $domains['one_example_com']->getPath() . 'node/1');
    $this->assertEquals($node->toUrl('canonical')->setAbsolute()->toString(), $domains['one_example_com']->getPath() . 'node/1');

    $node->set(DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD, 'two_example_com');
    $this->assertEquals($token_handler->replace('[node:canonical-source-domain-url]', ['node' => $node]), $domains['two_example_com']->getPath() . 'node/1');
    $this->assertEquals($node->toUrl('canonical')->setAbsolute()->toString(), $domains['two_example_com']->getPath() . 'node/1');

    // Exclude the canonical path from rewrites.
    $config = $this->config('domain_source.settings');
    $config->set('exclude_routes', ['canonical' => 'canonical'])->save();
    // Because of path cache, we have to flush here.
    drupal_flush_all_caches();

    // Test token value, and URL without token.
    $node->set(DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD, 'one_example_com');
    $one_example_com_absolute_url = $node->toUrl('canonical')->setAbsolute()->toString();
    $this->assertEquals($token_handler->replace('[node:canonical-source-domain-url]', ['node' => $node]), $domains['one_example_com']->getPath() . 'node/1');

    $node->set(DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD, 'two_example_com');
    $two_example_com_absolute_url = $node->toUrl('canonical')->setAbsolute()->toString();
    $this->assertEquals($token_handler->replace('[node:canonical-source-domain-url]', ['node' => $node]), $domains['two_example_com']->getPath() . 'node/1');

    $this->assertEquals($one_example_com_absolute_url, $two_example_com_absolute_url, 'Canonical url rewrite is not used, domain source change did not affect url.');
  }

}
