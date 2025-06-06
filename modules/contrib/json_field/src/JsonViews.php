<?php

declare(strict_types=1);

namespace Drupal\json_field;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\field\FieldStorageConfigInterface;

/**
 * Class JSONViews.
 *
 * @package Drupal\json_field
 */
class JsonViews implements JsonViewsInterface {

  use StringTranslationTrait;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a new MetatagDefaultsForm.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getViewsFieldData(FieldStorageConfigInterface $field_storage) {
    // Make sure views.views.inc is loaded.
    $this->moduleHandler->loadInclude('views', 'inc', 'views.views');

    // Get the default data from the views module.
    $data = views_field_default_views_data($field_storage);

    $field_name = $field_storage->getName();
    $value_field_name = $field_name . '_value';
    $entity_entry = $field_storage->getTargetEntityTypeId() . '__' . $field_name;

    if (!empty($data[$entity_entry][$value_field_name])) {
      $data[$entity_entry][$field_name . '_json_value'] = [
        'group' => $data[$entity_entry][$value_field_name]['group'],
        'title' => $this->t('@value_title (data)', [
          '@value_title' => $data[$entity_entry][$value_field_name]['title'],
        ]),
        'title short' => $data[$entity_entry][$value_field_name]['title short'],
        'help' => $data[$entity_entry][$value_field_name]['help'],
        'field' => $data[$entity_entry][$field_name]['field'],
      ];
      $data[$entity_entry][$field_name . '_json_value']['field']['id'] = 'json_data';
    }

    return $data;
  }

}
