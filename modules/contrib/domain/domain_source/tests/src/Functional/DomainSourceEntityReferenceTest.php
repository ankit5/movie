<?php

namespace Drupal\Tests\domain_source\Functional;

use Drupal\field\FieldConfigInterface;
use Drupal\Tests\domain\Functional\DomainTestBase;

/**
 * Tests the domain source entity reference field type.
 *
 * @group domain_source
 */
class DomainSourceEntityReferenceTest extends DomainTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain',
    'domain_source',
    'field',
    'field_ui',
    'menu_ui',
    'block',
  ];

  /**
   * Tests that the module installed its field correctly.
   */
  public function testDomainSourceNodeField() {
    $admin_user = $this->drupalCreateUser([
      'administer content types',
      'administer node fields',
      'administer node display',
      'administer domains',
    ]);
    $this->drupalLogin($admin_user);

    // Visit the article field administration page.
    $this->drupalGet('admin/structure/types/manage/article/fields');
    $this->assertSession()->statusCodeEquals(200);

    // Check for a domain field.
    $this->assertSession()->pageTextContains('Domain Source');

    // Visit the article field display administration page.
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertSession()->statusCodeEquals(200);

    // Check for a domain field.
    $this->assertSession()->pageTextContains('Domain Source');
  }

  /**
   * Tests the storage of the domain source field.
   */
  public function testDomainSourceFieldStorage() {
    $admin_user = $this->drupalCreateUser([
      'administer content types',
      'administer node fields',
      'administer node display',
      'administer domains',
      'administer menu',
      'create article content',
      'edit any article content',
    ]);
    $this->drupalLogin($admin_user);

    // Create 5 domains.
    $this->domainCreateTestDomains(5);

    // Visit the article field display administration page.
    $this->drupalGet('node/add/article');
    $this->assertSession()->statusCodeEquals(200);

    // Check the new field exists on the page.
    $this->assertSession()->pageTextContains('Domain Source');

    // We expect to find 5 domain options + none.
    $one = $two = $two_path = NULL;
    /** @var \Drupal\domain\DomainInterface[] $domains */
    $domains = \Drupal::entityTypeManager()->getStorage('domain')->loadMultiple();
    foreach ($domains as $domain) {
      $string = 'value="' . $domain->id() . '"';
      $this->assertSession()->responseContains($string);
      if (!isset($one)) {
        $one = $domain->id();
        continue;
      }
      if (!isset($two)) {
        $two = $domain->id();
        $two_path = $domain->getPath();
      }
    }
    $this->assertSession()->responseContains('value="_none"');

    $node_storage = \Drupal::entityTypeManager()->getStorage('node');

    // Try to post a node, assigned to the second domain.
    $edit = [
      'title[0][value]' => 'Test node',
      'field_domain_source' => $two,
    ];
    $this->drupalGet('node/add/article');
    $this->submitForm($edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);
    $node = $node_storage->load(1);
    // Check that the value is set.
    $value = domain_source_get($node);
    $this->assertEquals($two, $value, 'Node saved with proper source record.');

    // Test the URL.
    $url = $node->toUrl()->toString();
    $expected_url = $two_path . 'node/1';
    $this->assertEquals($expected_url, $url, 'URL rewritten correctly.');

    // Try to post a node, assigned to no domain.
    $edit = [
      'title[0][value]' => 'Test node',
      'field_domain_source' => '_none',
    ];
    $this->drupalGet('node/add/article');
    $this->submitForm($edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);
    $node = $node_storage->load(2);
    // Check that the value is set.
    $value = domain_source_get($node);
    $this->assertNull($value, 'Node saved with proper source record.');

    // Test the url.
    $url = $node->toUrl()->toString();
    $expected_url = base_path() . 'node/2';
    $this->assertEquals($expected_url, $url, 'URL rewritten correctly.');

    // Place the menu block.
    $this->drupalPlaceBlock('system_menu_block:main');

    // Enable main menu as available menu.
    $edit = [
      'menu_options[main]' => 1,
      'menu_parent' => 'main:',
    ];
    $this->drupalGet('admin/structure/types/manage/article');
    $this->submitForm($edit, 'Save');

    // Create a third node that is assigned to a menu.
    $edit = [
      'title[0][value]' => 'Node 3',
      'menu[enabled]' => 1,
      'menu[title]' => 'Test preview',
      'field_domain_source' => $two,
    ];
    $this->drupalGet('node/add/article');
    $this->submitForm($edit, 'Save');
    // Test the URL against expectations, and the rendered menu link.
    $node = $node_storage->load(3);
    $url = $node->toUrl()->toString();
    $expected_url = $two_path . 'node/3';
    $this->assertEquals($expected_url, $url, 'URL rewritten correctly.');
    // Load the page with a menu and check that link.
    $this->drupalGet('node/3');
    $this->assertSession()->responseContains('href="' . $url);

    // Remove the field from the node type and make sure nothing breaks.
    // See https://www.drupal.org/node/2892612
    $id = 'node.article.field_domain_source';
    $field = \Drupal::entityTypeManager()->getStorage('field_config')->load($id);
    if ($field instanceof FieldConfigInterface) {
      $field->delete();
      field_purge_batch(10, $field->uuid());
      drupal_flush_all_caches();
    }
    // Visit the article field display administration page.
    $this->drupalGet('node/add/article');
    $this->assertSession()->statusCodeEquals(200);
    // Try to post a node, assigned to no domain.
    $edit2 = [
      'title[0][value]' => 'Test node',
    ];
    $this->drupalGet('node/add/article');
    $this->submitForm($edit2, 'Save');
    // Test the URL against expectations, and the rendered menu link.
    $node = $node_storage->load(4);
    $url = $node->toUrl()->toString();
    $expected_url = base_path() . 'node/4';
    $this->assertEquals($expected_url, $url, 'No URL rewrite performed.');
  }

}
