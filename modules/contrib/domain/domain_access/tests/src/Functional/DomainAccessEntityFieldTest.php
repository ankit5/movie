<?php

namespace Drupal\Tests\domain_access\Functional;

use Drupal\Core\Language\LanguageInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\domain\Functional\DomainTestBase;

/**
 * Tests the domain access entity reference field type for custom entities.
 *
 * @group domain_access
 */
class DomainAccessEntityFieldTest extends DomainTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain',
    'domain_access',
    'domain_access_test',
    'field',
    'field_ui',
    'user',
    'taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create 5 domains.
    $this->domainCreateTestDomains(5);
  }

  /**
   * Tests that the fields are accessed properly.
   */
  public function testDomainAccessEntityFields() {
    // Create a vocabulary.
    $vocabulary = Vocabulary::create([
      'name' => 'Domain vocabulary',
      'description' => 'Test taxonomy for Domain Access',
      'vid' => 'domain_access',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
      'weight' => 100,
    ]);
    $vocabulary->save();
    $text = [
      'taxonomy_term' => [
        'name' => 'term',
        'label' => 'Send to all affiliates',
        'description' => 'Make this term available on all domains.',
      ],
    ];
    domain_access_confirm_fields('taxonomy_term', 'domain_access', $text);
    $admin_user = $this->drupalCreateUser([
      'bypass node access',
      'administer content types',
      'administer node fields',
      'administer node display',
      'administer domains',
      'publish to any domain',
      'administer taxonomy',
      'administer taxonomy_term fields',
      'administer taxonomy_term form display',
    ]);
    $this->drupalLogin($admin_user);
    $this->drupalGet('admin/structure/taxonomy/manage/domain_access/overview/fields');
    $this->assertSession()->statusCodeEquals(200);

    // Check for a domain field.
    $this->assertSession()->pageTextContains('Domain Access');
  }

}
