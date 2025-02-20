<?php

namespace Drupal\Tests\domain_source\Functional;

use Drupal\Core\Url;
use Drupal\domain_source\DomainSourceElementManagerInterface;
use Drupal\Tests\domain\Functional\DomainTestBase;

/**
 * Tests behavior for the rewriting links subject to Trusted Host settings.
 *
 * @group domain_source
 */
class DomainSourceTrustedHostTest extends DomainTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain',
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

    // Create 3 domains.
    DomainTestBase::domainCreateTestDomains(3);
  }

  /**
   * Tests domain source URLs.
   */
  public function testDomainSourceUrls() {
    // Create a node, assigned to a source domain.
    $id = 'one_example_com';

    $node_values = [
      'type' => 'page',
      'title' => 'foo',
      DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD => $id,
    ];
    $node = $this->createNode($node_values);

    // Variables for our tests.
    $path = 'node/1';
    /** @var \Drupal\domain\DomainInterface[] $domains */
    $domains = \Drupal::entityTypeManager()->getStorage('domain')->loadMultiple();
    $source = $domains[$id];
    $expected = $source->getPath() . $path;
    $route_name = 'entity.node.canonical';
    $route_parameters = ['node' => 1];
    $options = [];

    // Get the link using Url::fromRoute().
    $url = Url::fromRoute($route_name, $route_parameters, $options)->toString();
    $this->assertTrue($url === $expected, 'fromRoute');

    // Set up two additional domains.
    $domain2 = $domains['two_example_com'];

    // Check against trusted host patterns.
    $settings = ['settings' => []];
    $settings['settings']['trusted_host_patterns'] = (object) [
      'value' => ['^' . $this->prepareTrustedHostname($domain2->getHostname()) . '$'],
      'required' => TRUE,
    ];
    $this->writeSettings($settings);
    // This URL should fail due to trusted host omission.
    $this->drupalGet($url);
    $this->assertSession()->responseContains('The provided host name is not valid for this server.');

    // Now switch the node to a domain that is trusted.
    // @phpstan-ignore-next-line
    $node->{DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD} = $domain2->id();
    $node->save();
    // Get the link using Url::fromRoute().
    $expected = $domain2->getPath() . $path;
    $url = Url::fromRoute($route_name, $route_parameters, $options)->toString();
    // Assert that the URL is what we expect.
    $this->assertTrue($url === $expected, 'fromRoute');
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
  }

}
