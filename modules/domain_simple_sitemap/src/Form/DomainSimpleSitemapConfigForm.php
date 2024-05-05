<?php

namespace Drupal\domain_simple_sitemap\Form;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\domain\DomainInterface;
use Drupal\domain_simple_sitemap\DomainSitemapManager;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DomainSimpleSitemapConfigForm.
 *
 * @package Drupal\domain_simple_sitemap\Form
 */
class DomainSimpleSitemapConfigForm extends ConfigFormBase {

  /**
   * The DomainSitemapManager.
   *
   * @var \Drupal\domain_simple_sitemap\DomainSitemapManager
   */
  protected $domainSitemapManager;

  /**
   * Batch Builder.
   *
   * @var \Drupal\Core\Batch\BatchBuilder
   */
  protected $batchBuilder;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * DomainSimpleSitemapConfigForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\domain_simple_sitemap\DomainSitemapManager $domainSitemapManager
   *   The domainSitemapManager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    DomainSitemapManager $domainSitemapManager,
    EntityTypeManagerInterface $entityTypeManager,
    ModuleHandlerInterface $moduleHandler) {
    $this->domainSitemapManager = $domainSitemapManager;
    $this->moduleHandler = $moduleHandler;
    $this->entityTypeManager = $entityTypeManager;
    $this->batchBuilder = new BatchBuilder();
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('domain_simple_sitemap.manager'),
      $container->get('entity_type.manager'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'domain_simple_sitemap.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'domain_simple_sitemap_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['generate_domain_variants_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t("Generate domain's sitemap variants"),
      '#submit' => ['::generateDomainSitemapVariants'],
      '#validate' => [],
      '#preffix' => '<div>',
      '#suffix' => '</div><br\>',
    ];

    $form['domain_sitemap_variants'] = [
      '#markup' => Link::createFromRoute('You can check existing sitemap variants of domains', 'entity.simple_sitemap.collection')
        ->toString(),
    ];

    $config = $this->config('domain_simple_sitemap.settings');
    $form['domain_simple_sitemap_filter'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use node source instead of node access as filter'),
      '#default_value' => $config->get('domain_simple_sitemap_filter'),
      '#description' => $this->t('When checked the Domain Sitemap will be filtered by domain source.'),
    ];

    $form['domain_simple_sitemap_replace_homepage'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Replace default front page URL to base domain URL.'),
      '#default_value' => $config->get('domain_simple_sitemap_replace_homepage'),
      '#description' => $this->t('When checked the link in generated sitemap https://example.com/{domain_site_settings_default_front_page} will replaced by https://example.com.'),
    ];
    if ($this->moduleHandler->moduleExists('domain_config')) {
      $supported_module_found = TRUE;
    }
    elseif ($this->moduleHandler->moduleExists('domain_site_settings')) {
      $supported_module_found = TRUE;
    }
    else {
      $supported_module_found = FALSE;
    }
    if (!$supported_module_found) {
      $module_link = Link::fromTextAndUrl(
        $this->t('Domain Site Settings'),
        Url::fromUri(
          'https://www.drupal.org/project/domain_site_settings',
          [
            'absolute' => TRUE,
            'attributes' => [
              'target' => '_blank',
            ],
          ])
      )
        ->toString();
      $form['domain_simple_sitemap_replace_homepage']['#attributes']['disabled'][] = 'disabled';
      $form['domain_simple_sitemap_replace_homepage']['#description'] = $this->t('For this functionality needs to be enabled the Domain Config or @link module.',
        [
          '@link' => $module_link,
        ]
      );
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('domain_simple_sitemap.settings')
      ->set('domain_simple_sitemap_filter', (bool) $form_state->getValue('domain_simple_sitemap_filter'))
      ->set('domain_simple_sitemap_replace_homepage', (bool) $form_state->getValue('domain_simple_sitemap_replace_homepage'))
      ->save();

    parent::submitForm($form, $form_state);
    $config = $this->config('domain_simple_sitemap.settings');
    $config->save();
  }

  /**
   * Submit for the generate_domain_variants_submit action.
   *
   * @param array $form
   *   The form object.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form_state object.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function generateDomainSitemapVariants(array &$form, FormStateInterface $form_state) {
    $active_domains = $this->entityTypeManager
      ->getStorage('domain')
      ->loadByProperties(['status' => TRUE]);
    if (empty($active_domains)) {
      $this->messenger()->addMessage(
        $this->t('You have no active domains. Please check it.')
      );
    }
    $this->batchBuilder
      ->setTitle($this->t('Bulk creating sitemap variants for domain'))
      ->setInitMessage($this->t('Initializing.'))
      ->setProgressMessage($this->t('Completed @current of @total.'))
      ->setErrorMessage($this->t('An error has occurred.'));

    $this->batchBuilder->setFile(\Drupal::service('extension.list.module')->getPath('domain_simple_sitemap') . '/src/Form/DomainSimpleSitemapConfigForm.php');
    $this->batchBuilder->addOperation([
      $this,
      'batchProcess',
    ], [$active_domains]);
    $this->batchBuilder->setFinishCallback([$this, 'batchFinish']);
    batch_set($this->batchBuilder->toArray());
  }

  /**
   * Batch processing of creating sitemap variant for domain.
   *
   * @param array $domains
   *   The array of DomainInterface objects.
   * @param array|\ArrayAccess $context
   *   The batch context.
   */
  public function batchProcess(array $domains, &$context) {
    $limit = 10;

    if (empty($context['sandbox']['current'])) {
      $context['sandbox']['current'] = 0;
      $context['sandbox']['total'] = count($domains);
    }

    if (empty($context['sandbox']['items'])) {
      $context['sandbox']['items'] = $domains;
    }

    $counter = 0;
    if (!empty($context['sandbox']['items'])) {
      if ($context['sandbox']['current'] != 0) {
        array_splice($context['sandbox']['items'], 0, $limit);
      }
      foreach ($context['sandbox']['items'] as $domain) {
        if ($counter != $limit && $domain instanceof DomainInterface) {
          $this->domainSitemapManager->addSitemapVariant($domain);
          $counter++;
          $context['sandbox']['current']++;
          $context['message'] = $this->t('Now processing :domain domain.', [
            ':domain' => $domain->label(),
          ]);
          $context['results']['processed'][] = $domain;
        }
      }
    }
    if ($context['sandbox']['current'] != $context['sandbox']['total']) {
      $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['total'];
    }

  }

  /**
   * Finished callback for batch.
   *
   * @param bool $success
   *   Indicate that the batch API tasks were all completed successfully.
   * @param array $results
   *   An array of all the results that were updated in update_do_one().
   * @param array $operations
   *   A list of the operations that had not been completed by the batch API.
   */
  public function batchFinish(bool $success, array $results, array $operations) {
    if ($success) {
      foreach ($results['processed'] as $domain) {
        $this->messenger()
          ->addStatus(
            $this->t('Sitemap variant was created for domain @domain', [
              '@domain' => $domain->label(),
            ])
          );
      }
    }
    else {
      $this->messenger()
        ->addError(
          $this->t('An error occurred during batch process. Please check logs.')
        );
    }
  }

}
