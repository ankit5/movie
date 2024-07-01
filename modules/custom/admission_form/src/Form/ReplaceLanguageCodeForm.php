<?php

namespace Drupal\admission_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ReplaceLanguageCodeForm.
 *
 * @package Drupal\admission_form\Form
 */
class ReplaceLanguageCodeForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'replace_langcode_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['replace_language'] = [
      '#type' => 'submit',
      '#value' => $this->t('Replace Language code'),
    ];
    

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  /* $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
   $query->condition('type', 'movie', '=');
  $query->notExists('field_year');*/
 

$query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
   $query->condition('type', 'movie', '=');
    $query->condition('field_url', '%/series%', 'LIKE');
  //  $query->notExists('field_year');
  $query->sort('created', 'DESC');
   $nids = $query->range(0,300)->execute();

   $batch = [
     'title' => t('Replacing Language Code...'),
     'operations' => [],
     'finished' => '\Drupal\admission_form\ReplaceLanguageCode::replaceLangcodeFinishedCallback',
   ];
   foreach($nids as $nid) {
     $batch['operations'][] = ['\Drupal\admission_form\ReplaceLanguageCode::replaceLangcode', [$nid]];
   }

   batch_set($batch);
  }

}