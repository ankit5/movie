<?php

namespace Drupal\Tests\domain\Functional;

use Drupal\Core\Config\ConfigValueException;

/**
 * Tests domain record validation.
 *
 * @group domain
 */
class DomainValidatorTest extends DomainTestBase {

  /**
   * Tests that a domain hostname validates.
   */
  public function testDomainValidator() {
    // No domains should exist.
    $this->domainTableIsEmpty();
    $validator = \Drupal::service('domain.validator');
    /** @var \Drupal\domain\DomainStorageInterface $storage */
    $storage = \Drupal::entityTypeManager()->getStorage('domain');

    // Create a domain.
    $this->domainCreateTestDomains(1, 'foo.com');
    // Check the created domain based on its known id value.
    $key = 'foo.com';
    /** @var \Drupal\domain\Entity\Domain $domain */
    $domain = $storage->loadByHostname($key);
    $this->assertNotEmpty($domain, 'Test domain created.');

    // Valid hostnames to test. Valid is the boolean value.
    $hostnames = [
      'localhost' => 1,
      'example.com' => 1,
       // See www-prefix test, below.
      'www.example.com' => 1,
      'one.example.com' => 1,
      'example.com:8080' => 1,
       // Only one colon.
      'example.com::8080' => 0,
       // No letters after a colon.
      'example.com:abc' => 0,
       // Cannot begin with a dot.
      '.example.com' => 0,
       // Cannot end with a dot.
      'example.com.' => 0,
       // Lowercase only.
      'EXAMPLE.com' => 0,
       // ascii-only.
      'éxample.com' => 0,
    ];
    foreach ($hostnames as $hostname => $valid) {
      $errors = $validator->validate($hostname);
      if ($valid === 1) {
        $this->assertEmpty($errors, 'Validation correct with no errors.');
      }
      else {
        $this->assertNotEmpty($errors, 'Validation correct with proper errors.');
      }
    }
    // Test duplicate hostname creation.
    $test_hostname = 'foo.com';
    $test_domain = $storage->create([
      'hostname' => $test_hostname,
      'name' => 'Test domain',
      'id' => 'test_domain',
    ]);
    try {
      $test_domain->save();
      $this->fail('Duplicate hostname validation');
    }
    catch (ConfigValueException $e) {
      $expected_message = "The hostname ($test_hostname) is already registered.";
      $this->assertEquals($expected_message, $e->getMessage());
    }
    // Test the two configurable options.
    $config = $this->config('domain.settings');
    $config->set('www_prefix', TRUE);
    $config->set('allow_non_ascii', TRUE);
    $config->save();
    // Valid hostnames to test. Valid is the boolean value.
    $hostnames = [
       // No www-prefix allowed.
      'www.example.com' => 0,
       // ascii-only allowed.
      'éxample.com' => 1,
    ];
    foreach ($hostnames as $hostname => $valid) {
      $errors = $validator->validate($hostname);
      if ($valid) {
        $this->assertEmpty($errors, 'Validation test correct with no errors.');
      }
      else {
        $this->assertNotEmpty($errors, 'Validation test correct with errors.');
      }
    }
  }

}
