<?php

namespace Drupal\Tests\domain_config_ui\FunctionalJavascript;

use Drupal\domain_config_ui\DomainConfigUITrait;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\domain\Traits\DomainTestTrait;
use Drupal\Tests\domain_config_ui\Traits\DomainConfigUITestTrait;

/**
 * Tests the domain config settings interface.
 *
 * @group domain_config_ui
 */
class DomainConfigUISettingsTest extends WebDriverTestBase {

  use DomainConfigUITrait;
  use DomainConfigUITestTrait;
  use DomainTestTrait;

  /**
   * Disabled config schema checking.
   *
   * Domain Config actually duplicates schemas provided by other modules,
   * so it cannot define its own.
   *
   * @var bool
   */
  protected $strictConfigSchema = FALSE; // phpcs:ignore

  /**
   * The default theme.
   *
   * @var string
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain_config_ui',
    'language',
  ];

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->createAdminUser();
    $this->createEditorUser();

    $this->setBaseHostname();
    $this->domainCreateTestDomains(5);

    $this->createLanguage();
  }

  /**
   * Tests ability to add/remove forms.
   */
  public function testSettings() {
    $config = $this->config('domain_config_ui.settings');
    $expected = $this->explodePathSettings("/admin/appearance\r\n/admin/config/system/site-information");
    $value = $this->explodePathSettings($config->get('path_pages'));
    $this->assertEquals($expected, $value);

    // Test with language and without.
    foreach (['en', 'es'] as $langcode) {
      $config->save();
      $prefix = '';
      if ($langcode === 'es') {
        $prefix = '/es';
      }
      $this->drupalLogin($this->adminUser);
      // Test some theme paths.
      $path = $prefix . '/admin/appearance';
      $this->drupalGet($path);
      $this->assertSession()->linkExists('Disable domain configuration');

      $path = $prefix . '/admin/appearance/settings/stark';
      $this->drupalGet($path);
      $page = $this->getSession()->getPage();
      $page->clickLink('Enable domain configuration');
      // @phpstan-ignore-next-line
      $this->assertSession()->waitForLink('Disable domain configuration');

      $this->drupalGet($path);
      $config2 = $this->config('domain_config_ui.settings');
      $expected2 = $this->explodePathSettings("/admin/appearance\r\n/admin/config/system/site-information\r\n/admin/appearance/settings/stark");
      $value2 = $this->explodePathSettings($config2->get('path_pages'));
      $this->assertEquals($expected2, $value2);

      // Test removal of paths.
      $this->drupalGet($path);
      $page = $this->getSession()->getPage();
      $page->clickLink('Disable domain configuration');
      // @phpstan-ignore-next-line
      $this->assertSession()->waitForLink('Enable domain configuration');

      $path = $prefix . '/admin/config/system/site-information';
      $this->drupalGet($path);
      $page = $this->getSession()->getPage();
      $page->clickLink('Disable domain configuration');
      // @phpstan-ignore-next-line
      $this->assertSession()->waitForLink('Enable domain configuration');

      $expected3 = $this->explodePathSettings("/admin/appearance");
      $config3 = $this->config('domain_config_ui.settings');
      $value3 = $this->explodePathSettings($config3->get('path_pages'));
      $this->assertEquals($expected3, $value3);

      $this->drupalGet($path);
      $page = $this->getSession()->getPage();
      $page->findLink('Enable domain configuration');

      // Ensure the editor cannot access the form.
      $this->drupalLogin($this->editorUser);
      $this->drupalGet($path);
      $this->assertSession()->pageTextNotContains('Enable domain configuration');
    }
  }

}
