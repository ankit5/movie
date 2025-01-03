<?php

namespace Drupal\Tests\domain_access\Functional;

use Drupal\Core\Url;
use Drupal\domain_access\DomainAccessManagerInterface;
use Drupal\Tests\domain\Functional\DomainTestBase;

/**
 * Tests behavior for getting all URLs for an entity.
 *
 * @group domain_access
 */
class DomainAccessContentUrlsTest extends DomainTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain',
    'domain_access',
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
    DomainTestBase::domainCreateTestDomains(4);
  }

  /**
   * Tests domain source URLs.
   */
  public function testDomainContentUrls() {
    // Create a node, assigned to a source domain.
    $id = 'one_example_com';

    $nodes_values = [
      'type' => 'page',
      'title' => 'foo',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [
        'example_com',
        'one_example_com',
        'two_example_com',
      ],
      DomainAccessManagerInterface::DOMAIN_ACCESS_ALL_FIELD => 0,
    ];
    $node = $this->createNode($nodes_values);

    // Variables for our tests.
    $path = 'node/1';
    /** @var \Drupal\domain\DomainInterface[] $domains */
    $domains = \Drupal::entityTypeManager()->getStorage('domain')->loadMultiple();
    $route_name = 'entity.node.canonical';
    $route_parameters = ['node' => 1];
    $uri = 'entity:' . $path;
    $uri_path = '/' . $path;
    $expected = base_path() . $path;
    $options = [];

    // Get the link using Url::fromRoute().
    $url = Url::fromRoute($route_name, $route_parameters, $options)->toString();
    $this->assertEquals($expected, $url, 'fromRoute');

    // Get the link using Url::fromUserInput()
    $url = Url::fromUserInput($uri_path, $options)->toString();
    $this->assertEquals($expected, $url, 'fromUserInput');

    // Get the link using Url::fromUri()
    $url = Url::fromUri($uri, $options)->toString();
    $this->assertEquals($expected, $url, 'fromUri');

    // Get the path processor service.
    $paths = \Drupal::service('domain_access.manager');
    $urls = $paths->getContentUrls($node);
    $expected = [
      'example_com' => $domains['example_com']->getPath() . 'node/1',
      $id => $domains[$id]->getPath() . 'node/1',
      'two_example_com' => $domains['two_example_com']->getPath() . 'node/1',
    ];
    $this->assertEquals($expected, $urls);
  }

}
