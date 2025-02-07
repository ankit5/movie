<?php

namespace Drupal\Tests\domain\Functional;

/**
 * Tests the domain record response check.
 *
 * @group domain
 */
class DomainCheckResponseTest extends DomainTestBase {

  /**
   * Tests that a domain responds as expected.
   */
  public function testDomainCheckResponse() {
    $perms = ['administer domains', 'create domains'];
    $admin_user = $this->drupalCreateUser($perms);
    $this->drupalLogin($admin_user);

    /** @var \Drupal\domain\DomainStorageInterface $storage */
    $storage = \Drupal::entityTypeManager()->getStorage('domain');

    // Make a POST request on admin/config/domain/add.
    $edit = $this->domainPostValues();
    // Use hostname with dot (.) to avoid validation error.
    $edit['hostname'] = 'example.com';
    $this->drupalGet('admin/config/domain/add');
    $this->submitForm($edit, 'Save');
    // Did it save correctly?
    $this->assertSession()->responseNotContains('The server request to');
    $domains = $this->getDomains();
    $this->assertCount(1, $domains, 'Domain record saved via form.');

    // Make an invalid POST request on admin/config/domain/add.
    // We can no longer test this with Guzzle, so we only test the bypass.
    $edit = $this->domainPostValues();
    // Set a hostname that does not exist on the server.
    $edit['hostname'] = 'foo.bar';
    $edit['id'] = $storage->createMachineName($edit['hostname']);
    $edit['validate_url'] = 1;
    try {
      $this->drupalGet('admin/config/domain/add');
      $this->submitForm($edit, 'Save');
    }
    catch (\Exception $e) {
      // Ensure no test errors.
    }
    // The domain should not save.
    $this->assertSession()->responseContains('The server request to');
    $domains = $this->getDomains();
    $this->assertCount(1, $domains, 'Domain record not saved via form.');

    // Bypass the check.
    $edit['validate_url'] = 0;
    $this->drupalGet('admin/config/domain/add');
    $this->submitForm($edit, 'Save');

    // The domain should save.
    $this->assertSession()->responseNotContains('The server request to');
    $domains = $this->getDomains();
    $this->assertCount(2, $domains, 'Domain record saved via form.');
  }

}
