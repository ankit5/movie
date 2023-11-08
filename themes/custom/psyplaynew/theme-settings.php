<?php
use Drupal\Core\Form\FormStateInterface;
/**
 * @file
 * Custom setting for gypsum theme.
 */
function psyplaynew_form_system_theme_settings_alter(&$form, FormStateInterface &$form_state, $form_id = NULL) {

 $form['movie'] = [
    '#type'        => 'details',
    '#title'       => t("Get Content Movie Replace Name"),
  ];
 $form['movie']['old_domain_name'] = [
    '#type'          => 'textfield',
    '#title'         => t('Old Domain name'),
    '#description'   => t("Enter Old Domain name."),
    '#default_value' => theme_get_setting('old_domain_name'),
    '#attributes' => array('maxlength' => 255),
  ];
  

  $form['movie']['new_domain_name'] = [
    '#type'          => 'textfield',
    '#title'         => t('New Domain name'),
    '#description'   => t("Enter New Domain name."),
    '#default_value' => theme_get_setting('new_domain_name'),
  ];

  $form['movie_iframe'] = [
    '#type'        => 'details',
    '#title'       => t("Iframe Movie Replace Name"),
  ];

  $form['movie_iframe']['iframe_old_domain_name'] = [
    '#type'          => 'textfield',
    '#title'         => t('Iframe Old Domain name'),
    '#description'   => t("Enter Iframe Old Domain name."),
    '#default_value' => theme_get_setting('iframe_old_domain_name'),
  ];

  $form['movie_iframe']['iframe_new_domain_name'] = [
    '#type'          => 'textfield',
    '#title'         => t('Iframe New Domain name'),
    '#description'   => t("Enter Iframe New Domain name."),
    '#default_value' => theme_get_setting('iframe_new_domain_name'),
  ];


}