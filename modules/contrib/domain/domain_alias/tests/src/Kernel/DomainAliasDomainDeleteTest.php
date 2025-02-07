<?php

namespace Drupal\Tests\domain_alias\Kernel;

use Drupal\Tests\domain\Functional\DomainTestBase;
use Drupal\Tests\domain_alias\Traits\DomainAliasTestTrait;

/**
 * Tests that aliases are deleted on domain delete.
 *
 * @group domain_alias
 */
class DomainAliasDomainDeleteTest extends DomainTestBase {

  use DomainAliasTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['domain', 'domain_alias'];

  /**
   * The Domain storage handler service.
   *
   * @var \Drupal\domain\DomainStorageInterface
   */
  public $domainStorage;

  /**
   * The Domain alias storage handler service.
   *
   * @var \Drupal\domain_alias\DomainAliasStorageInterface
   */
  public $aliasStorage;

  /**
   * Test setup.
   */
  protected function setUp(): void {
    parent::setUp();

    // Create a domain.
    $this->domainCreateTestDomains(2);

    // Get the services.
    $this->domainStorage = \Drupal::entityTypeManager()->getStorage('domain');
    $this->aliasStorage = \Drupal::entityTypeManager()->getStorage('domain_alias');
  }

  /**
   * Tests alias deletion on domain deletion.
   */
  public function testDomainDelete() {
    $domains = $this->domainStorage->loadMultiple();
    $patterns = [
      'example_com' => '*.example.com',
      'one_example_com' => 'foo.example.com',
    ];

    // Create an alias.
    $id = $domain = NULL;
    // @phpstan-ignore-next-line
    foreach ($domains as $id => $domain) {
      $values = [
        'domain_id' => $domain->id(),
        'pattern' => $patterns[$id],
        'redirect' => 0,
        'environment' => 'local',
      ];
      $this->createDomainAlias($values);
      $alias = $this->aliasStorage->loadByPattern($patterns[$id]);
      $this->assertNotEmpty($alias, 'Alias saved properly');
    }

    // Delete one domain.
    $domain->delete();
    $alias = $this->aliasStorage->loadByPattern($patterns[$id]);
    $this->assertEmpty($alias, 'Alias deleted properly');

    // Check the remaining domain, which should still have an alias.
    $domain = $this->domainStorage->load('example_com');
    $alias = $this->aliasStorage->loadByPattern($patterns[$domain->id()]);
    $this->assertNotEmpty($alias, 'Alias retained properly');
  }

}
