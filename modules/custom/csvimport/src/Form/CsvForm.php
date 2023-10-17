<?php

namespace Drupal\csvimport\Form;

use Drupal\Component\Utility\Environment;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CsvForm
 *
 * @package Drupal\csvimport\Form
 */
class CsvForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csvimport_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

   

    $form['csvfile'] = [
      '#type'              => 'managed_file',
      '#title'             => t('Upload file here'),
      '#upload_location'   => 'public://importcsv/',
      '#default_value'     => '',
      "#upload_validators" => ["file_validate_extensions" => ["csv"]],
      '#states'            => [
        'visible' => [
          ':input[name="File_type"]' => ['value' => t('Upload Your File')],
        ],
      ],
    ];

    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('Start Import'),
    ];

    return $form;
  }

 

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if ($csvupload = $form_state->getValue('csvupload')) {

      if ($handle = fopen($csvupload, 'r')) {

        if ($line = fgetcsv($handle, 4096)) {

          // Validate the uploaded CSV here.
          // The example CSV happens to have cell A1 ($line[0]) as
          // below; we validate it only.
          //
          // You'll probably want to check several headers, eg:
          // @codingStandardsIgnoreStart
          // if ( $line[0] == 'Index' || $line[1] != 'Supplier' || $line[2] != 'Title' )
          // @codingStandardsIgnoreEnd
          if ($line[0] != 'title') {
            $form_state->setErrorByName('csvfile', $this->t('Sorry, this file does not match the expected format.'));
          }
        }
        fclose($handle);
      }
      else {
        $form_state->setErrorByName('csvfile', $this->t('Unable to read uploaded file @filepath', ['@filepath' => $csvupload]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $batch = [
      'title'            => $this->t('Importing CSV ...'),
      'operations'       => [],
      'init_message'     => $this->t('Commencing'),
      'progress_message' => $this->t('Processed @current out of @total.'),
      'error_message'    => $this->t('An error occurred during processing'),
      'finished'         => '\Drupal\csvimport\Batch\CsvImportBatch::csvimportImportFinished',
    ];

    if ($csvupload = $form_state->getValue('csvupload')) {

      if ($handle = fopen($csvupload, 'r')) {

        $batch['operations'][] = [
          '\Drupal\csvimport\Batch\CsvImportBatch::csvimportRememberFilename',
          [$csvupload],
        ];

        while ($line = fgetcsv($handle, 4096)) {

          // Use base64_encode to ensure we don't overload the batch
          // processor by stuffing complex objects into it.
          $batch['operations'][] = [
            '\Drupal\csvimport\Batch\CsvImportBatch::csvimportImportLine',
            [array_map('base64_encode', $line)],
          ];
        }

        fclose($handle);
      }
    }

    batch_set($batch);
  }

}
