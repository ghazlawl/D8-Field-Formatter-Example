<?php

namespace Drupal\oms_gallery_field_formatter\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "oms_gallery_field_formatter",
 *   label = @Translation("OMS Gallery"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class GalleryFieldWidget extends \Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    // Custom widget-specific settings go here. Do not confuse this with formatter settings.
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    // Custom widget-specific settings go here. Do not confuse this with formatter settings.
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    // Custom widget-specific settings go here. Do not confuse this with formatter settings.
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    return $element;
  }
}
