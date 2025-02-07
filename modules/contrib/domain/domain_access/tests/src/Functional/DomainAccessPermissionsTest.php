<?php

namespace Drupal\Tests\domain_access\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\domain_access\DomainAccessManager;
use Drupal\domain_access\DomainAccessManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\Tests\domain\Functional\DomainTestBase;
use Drupal\user\RoleInterface;

/**
 * Tests the domain access integration with node_access callbacks.
 *
 * @group domain_access
 */
class DomainAccessPermissionsTest extends DomainTestBase {

  /**
   * The Entity access control handler.
   *
   * @var \Drupal\Core\Entity\EntityAccessControlHandlerInterface
   */
  protected $accessHandler;

  /**
   * The Domain Access manager.
   *
   * @var \Drupal\domain_access\DomainAccessManagerInterface
   */
  protected $manager;

  /**
   * An array of domains created for the tests.
   *
   * @var \Drupal\domain\DomainInterface
   */
  protected $domains;

  /**
   * The user storage handler.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['domain', 'domain_access', 'field', 'node'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Clear permissions for authenticated users.
    $this->config('user.role.' . RoleInterface::AUTHENTICATED_ID)->set('permissions', [])->save();
    // Create Basic page node type.
    if ($this->profile !== 'standard') {
      $this->drupalCreateContentType([
        'type' => 'page',
        'name' => 'Basic page',
        'display_submitted' => FALSE,
      ]);
      $this->drupalCreateContentType([
        'type' => 'article',
        'name' => 'Article',
        'display_submitted' => FALSE,
      ]);
    }
    $this->accessHandler = \Drupal::entityTypeManager()->getAccessControlHandler('node');
    $this->manager = \Drupal::service('domain_access.manager');
    $this->userStorage = \Drupal::entityTypeManager()->getStorage('user');
    // Create 5 domains.
    $this->domainCreateTestDomains(5);
    $this->domains = $domains = \Drupal::entityTypeManager()->getStorage('domain')->loadMultiple();
  }

  /**
   * Runs basic tests for node_access function.
   */
  public function testDomainAccessPermissions() {
    // Note that these are hook_node_access() rules. Node Access system tests
    // are in DomainAccessRecordsTest.
    // We expect to find 5 domain options. Set two for later use.
    $one = $two = NULL;
    foreach ($this->domains as $domain) {
      if (!isset($one)) {
        $one = $domain->id();
        continue;
      }
      if (!isset($two)) {
        $two = $domain->id();
      }
    }

    // Assign our user to domain $two. Test on $one and $two.
    $domain_user1 = $this->drupalCreateUser([
      'access content',
      'edit domain content',
      'delete domain content',
    ]);
    $this->addDomainsToEntity('user', $domain_user1->id(), $two, DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD);
    $domain_user1 = $this->userStorage->load($domain_user1->id());
    $assigned = DomainAccessManager::getAccessValues($domain_user1);
    $this->assertCount(1, $assigned, 'User assigned to one domain.');
    $this->assertArrayHasKey($two, $assigned, 'User assigned to proper test domain.');
    // Assign one node to default domain, and one to our test domain.
    $domain_node1 = $this->drupalCreateNode([
      'type' => 'page',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [$one],
    ]);
    $domain_node2 = $this->drupalCreateNode([
      'type' => 'page',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [$two],
    ]);
    $assigned = DomainAccessManager::getAccessValues($domain_node1);
    $this->assertArrayHasKey($one, $assigned, 'Node1 assigned to proper test domain.');
    $assigned = DomainAccessManager::getAccessValues($domain_node2);
    $this->assertArrayHasKey($two, $assigned, 'Node2 assigned to proper test domain.');

    // Tests 'edit domain content' to edit content assigned to their domains.
    $this->assertNodeAccess([
      'view' => TRUE,
      'update' => FALSE,
      'delete' => FALSE,
    ], $domain_node1, $domain_user1);
    $this->assertNodeAccess([
      'view' => TRUE,
      'update' => TRUE,
      'delete' => TRUE,
    ], $domain_node2, $domain_user1);

    // Tests 'edit domain TYPE content'.
    // Assign our user to domain $two. Test on $one and $two.
    $domain_user3 = $this->drupalCreateUser([
      'access content',
      'update page content on assigned domains',
      'delete page content on assigned domains',
    ]);
    $this->addDomainsToEntity('user', $domain_user3->id(), $two, DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD);
    $domain_user3 = $this->userStorage->load($domain_user3->id());
    $assigned = DomainAccessManager::getAccessValues($domain_user3);
    $this->assertCount(1, $assigned, 'User assigned to one domain.');
    $this->assertArrayHasKey($two, $assigned, 'User assigned to proper test domain.');

    // Assign two different node types to our test domain.
    $domain_node3 = $this->drupalCreateNode([
      'type' => 'article',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [$two],
    ]);
    $domain_node4 = $this->drupalCreateNode([
      'type' => 'page',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [$two],
    ]);
    $assigned = DomainAccessManager::getAccessValues($domain_node3);
    $this->assertArrayHasKey($two, $assigned, 'Node3 assigned to proper test domain.');
    $assigned = DomainAccessManager::getAccessValues($domain_node4);
    $this->assertArrayHasKey($two, $assigned, 'Node4 assigned to proper test domain.');

    // Tests 'edit TYPE content on assigned domains'.
    $this->assertNodeAccess([
      'view' => TRUE,
      'update' => FALSE,
      'delete' => FALSE,
    ], $domain_node3, $domain_user3);
    $this->assertNodeAccess([
      'view' => TRUE,
      'update' => TRUE,
      'delete' => TRUE,
    ], $domain_node4, $domain_user3);

    // @todo Test edit and delete for user with 'all affiliates' permission.
    // Tests 'edit domain TYPE content'.
    // Assign our user to domain $two. Test on $one and $two.
    $domain_user4 = $this->drupalCreateUser([
      'access content',
      'update page content on assigned domains',
      'delete page content on assigned domains',
    ]);
    $this->addDomainsToEntity('user', $domain_user4->id(), $two, DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD);
    $this->addDomainsToEntity('user', $domain_user4->id(), 1, DomainAccessManagerInterface::DOMAIN_ACCESS_ALL_FIELD);
    $domain_user4 = $this->userStorage->load($domain_user4->id());
    $assigned = DomainAccessManager::getAccessValues($domain_user4);
    $this->assertCount(1, $assigned, 'User assigned to one domain.');
    $this->assertArrayHasKey($two, $assigned, 'User assigned to proper test domain.');
    $this->assertNotEmpty($domain_user4->get(DomainAccessManagerInterface::DOMAIN_ACCESS_ALL_FIELD)->value, 'User assign to all affiliates.');

    // Assign two different node types to our test domain.
    $domain_node5 = $this->drupalCreateNode([
      'type' => 'article',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [$one],
    ]);
    $domain_node6 = $this->drupalCreateNode([
      'type' => 'page',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [$one],
    ]);
    $assigned = DomainAccessManager::getAccessValues($domain_node5);
    $this->assertArrayHasKey($one, $assigned, 'Node5 assigned to proper test domain.');
    $assigned = DomainAccessManager::getAccessValues($domain_node6);
    $this->assertArrayHasKey($one, $assigned, 'Node6 assigned to proper test domain.');

    // Tests 'edit TYPE content on assigned domains'.
    $this->assertNodeAccess([
      'view' => TRUE,
      'update' => FALSE,
      'delete' => FALSE,
    ], $domain_node5, $domain_user4);
    $this->assertNodeAccess([
      'view' => TRUE,
      'update' => TRUE,
      'delete' => TRUE,
    ], $domain_node6, $domain_user4);

  }

  /**
   * Tests domain access create permissions.
   */
  public function testDomainAccessCreatePermissions() {
    $one = $two = NULL;
    foreach ($this->domains as $domain) {
      if (!isset($one)) {
        $one = $domain->id();
        continue;
      }
      if (!isset($two)) {
        $two = $domain->id();
      }
    }
    // Tests create permissions. Any content on assigned domains.
    $perms = ['access content', 'create domain content'];
    $domain_account5 = $this->drupalCreateUser($perms);
    $this->addDomainsToEntity('user', $domain_account5->id(), $two, DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD);
    $domain_user5 = $this->userStorage->load($domain_account5->id());
    $assigned = DomainAccessManager::getAccessValues($domain_user5);
    $this->assertCount(1, $assigned, 'User assigned to one domain.');
    $this->assertArrayHasKey($two, $assigned, 'User assigned to proper test domain.');
    // This test is domain sensitive.
    foreach ($this->domains as $domain) {
      $this->domainLogin($domain, $domain_account5);
      $url = $domain->getPath() . 'node/add/page';
      $this->drupalGet($url);
      if ($domain->id() === $two) {
        $this->assertSession()->statusCodeEquals(200);
      }
      else {
        $this->assertSession()->statusCodeEquals(403);
      }
      // The user should be allowed to create articles.
      $url = $domain->getPath() . 'node/add/article';
      $this->drupalGet($url);
      if ($domain->id() === $two) {
        $this->assertSession()->statusCodeEquals(200);
      }
      else {
        $this->assertSession()->statusCodeEquals(403);
      }
    }

  }

  /**
   * Tests domain access limited create permissions.
   */
  public function testDomainAccessLimitedCreatePermissions() {
    $one = $two = NULL;
    foreach ($this->domains as $domain) {
      if (!isset($one)) {
        $one = $domain->id();
        continue;
      }
      if (!isset($two)) {
        $two = $domain->id();
      }
    }
    // Tests create permissions. Any content on assigned domains.
    $perms = ['access content', 'create page content on assigned domains'];
    $domain_account6 = $this->drupalCreateUser($perms);
    $this->addDomainsToEntity('user', $domain_account6->id(), $two, DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD);
    $domain_user6 = $this->userStorage->load($domain_account6->id());
    $assigned = DomainAccessManager::getAccessValues($domain_user6);
    $this->assertCount(1, $assigned, 'User assigned to one domain.');
    $this->assertArrayHasKey($two, $assigned, 'User assigned to proper test domain.');
    // This test is domain sensitive.
    foreach ($this->domains as $domain) {
      $this->domainLogin($domain, $domain_account6);
      $url = $domain->getPath() . 'node/add/page';
      $this->drupalGet($url);
      if ($domain->id() === $two) {
        $this->assertSession()->statusCodeEquals(200);
      }
      else {
        $this->assertSession()->statusCodeEquals(403);
      }
      // The user should not be allowed to create articles.
      $url = $domain->getPath() . 'node/add/article';
      $this->drupalGet($url);
      $this->assertSession()->statusCodeEquals(403);
    }
  }

  /**
   * Asserts that node access correctly grants or denies access.
   *
   * @param array $ops
   *   An associative array of the expected node access grants for the node
   *   and account, with each key as the name of an operation (e.g. 'view',
   *   'delete') and each value a Boolean indicating whether access to that
   *   operation should be granted.
   * @param \Drupal\node\NodeInterface $node
   *   The node object to check.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account for which to check access.
   */
  public function assertNodeAccess(array $ops, NodeInterface $node, AccountInterface $account) {
    foreach ($ops as $op => $result) {
      $this->assertEquals($result, $this->accessHandler->access($node, $op, $account), 'Expected result returned.');
    }
  }

}
