<?php

namespace Drupal\Tests\contact_storage\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests contact_storage ID field.
 *
 * @group contact_storage
 */
class ContactStorageFieldTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['contact', 'user', 'system'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('contact_message');
  }

  /**
   * Covers contact_storage_mode_preinstall().
   */
  public function testContactIdFieldIsCreated() {
    $this->container->get('module_installer')->install(['contact_storage']);
    // There should be no updates as contact_storage_module_pre_install() should
    // have applied the new field.
    $this->assertTrue(empty($this->container->get('entity.definition_update_manager')->needsUpdates()['contact_message']));
    $this->assertTrue(!empty($this->container->get('entity_field.manager')->getFieldStorageDefinitions('contact_message')['id']));
  }

}
