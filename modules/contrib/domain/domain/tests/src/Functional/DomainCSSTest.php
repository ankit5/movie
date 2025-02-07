<?php

namespace Drupal\Tests\domain\Functional;

use Drupal\Component\Utility\Html;

/**
 * Tests the domain CSS configuration.
 *
 * @group domain
 */
class DomainCSSTest extends DomainTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['domain'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    \Drupal::service('theme_installer')->install(['olivero']);
  }

  /**
   * Tests the handling of an inbound request.
   */
  public function testDomainNegotiator() {
    // No domains should exist.
    $this->domainTableIsEmpty();

    // Create four new domains programmatically.
    $this->domainCreateTestDomains(4);

    // The test runner doesn't use a theme that contains the preprocess hook,
    // so set to use Olivero.
    $config = $this->config('system.theme');
    $config->set('default', 'olivero')->save();

    // Test the response of the default home page.
    foreach ($this->getDomains() as $domain) {
      $this->drupalGet($domain->getPath());
      $text = '<body class="' . Html::getClass($domain->id() . '-class');
      $this->assertSession()->responseNotContains($text);
    }
    // Set the css classes.
    $config = $this->config('domain.settings');
    $config->set('css_classes', '[domain:machine-name]-class')->save();

    // Test the response of the default home page.
    foreach ($this->getDomains() as $domain) {
      // The render cache trips up this test. In production, it may be
      // necessary to add the url.site cache context. See README.md.
      drupal_flush_all_caches();
      $this->drupalGet($domain->getPath());
      $text = '<body class="' . Html::getClass($domain->id() . '-class');
      $this->assertSession()->responseContains($text);
    }

    // Set the css classes.
    $config = $this->config('domain.settings');
    $config->set('css_classes', '[domain:machine-name]-class [domain:name]-class')->save();
    // Test the response of the default home page.
    foreach ($this->getDomains() as $domain) {
      // The render cache trips up this test. In production, it may be
      // necessary to add the url.site cache context. See README.md.
      drupal_flush_all_caches();
      $this->drupalGet($domain->getPath());
      $text = '<body class="' . Html::getClass($domain->id() . '-class') . ' ' . Html::getClass($domain->label() . '-class');
      $this->assertSession()->responseContains($text);
    }

  }

}
