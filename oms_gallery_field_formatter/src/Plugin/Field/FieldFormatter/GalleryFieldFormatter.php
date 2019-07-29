<?php

namespace Drupal\oms_gallery_field_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;

/**
 * @FieldFormatter(
 *   id = "oms_gallery_field_formatter",
 *   label = @Translation("OMS Gallery"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class GalleryFieldFormatter extends \Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['image_style'] = 'slide';
    $settings['interval'] = 0;
    $settings['show_arrows'] = 'yes';
    $settings['show_indicators'] = 'yes';
    $settings['show_captions'] = 'yes';
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    // Load the image styles.
    $image_styles = ImageStyle::loadMultiple();

    // Hold the list of image styles for the user to choose from.
    $image_style_options = [];

    foreach ($image_styles as $k => $v) {
      // Add each image style to the dropdown.
      $image_style_options[$k] = $v->label();
    }

    $elements['image_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Image Style'),
      '#default_value' => $this->getSetting('image_style'),
      '#required' => TRUE,
      '#options' => $image_style_options,
    ];

    $elements['interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Slide Change Interval'),
      '#default_value' => $this->getSetting('interval'),
      '#required' => TRUE,
      '#min' => 0,
      '#description' => $this->t('Set to zero to disable auto-start.'),
    ];

    $elements['show_arrows'] = [
      '#type' => 'select',
      '#title' => $this->t('Show Arrows?'),
      '#default_value' => $this->getSetting('show_arrows'),
      '#required' => TRUE,
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
    ];

    $elements['show_indicators'] = [
      '#type' => 'select',
      '#title' => $this->t('Show Indicators?'),
      '#default_value' => $this->getSetting('show_indicators'),
      '#required' => TRUE,
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
    ];

    $elements['show_captions'] = [
      '#type' => 'select',
      '#title' => $this->t('Show Captions?'),
      '#default_value' => $this->getSetting('show_captions'),
      '#required' => TRUE,
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Image Style: @value', array('@value' => $this->getSetting('image_style')));
    $summary[] = $this->t('Slide Change Interval: @value', array('@value' => $this->getSetting('interval')));
    $summary[] = $this->t('Show Arrows: @value', array('@value' => $this->getSetting('show_arrows') == 'yes' ? 'Yes' : 'No'));
    $summary[] = $this->t('Show Indicators: @value', array('@value' => $this->getSetting('show_indicators') == 'yes' ? 'Yes' : 'No'));
    $summary[] = $this->t('Show Captions: @value', array('@value' => $this->getSetting('show_captions') == 'yes' ? 'Yes' : 'No'));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Get a unique ID for this gallery.
    $id = 'oms-gallery-' . rand(1000, 9999);

    // Hold the initial elements.
    $elements = [];

    if (isset($items) && $items->count() > 0) {
      // Build the container.
      $elements = [
        '#theme' => 'oms_gallery_container',
        '#id' => $id,
        '#interval' => $this->getSetting('interval'),
        '#indicators' => [],
        '#slides' => [],
        // Not really used for theming but nice to have in case you need it.
        '#show_arrows' => $this->getSetting('show_arrows'),
        '#show_indicators' => $this->getSetting('show_indicators'),
        '#show_captions' => $this->getSetting('show_captions'),
      ];

      // Hold the slide index.
      $i = 0;

      // Add the slides and indicators to the container.
      foreach ($items as $delta => $item) {
        // Get the target property this Entity Reference field points to.
        $target = $item->get('target_id');

        // Get the nid.
        $nid = $target->getValue();

        // Load the node.
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $node = $node_storage->load($nid);

        // Get the image field.
        // Borrowed image style code from http://www.vdmi.nl/blog/drupal-8-rendering-image-programmatically
        $image_field = $node->get('field_image');
        $image = $image_field->view();
        $image[0]['#image_style'] = $this->getSetting('image_style');
        $image['#label_display'] = 'hidden';

        // Add this slide to the container.
        $elements['#slides'][$delta] = [
          '#theme' => 'oms_gallery_slide',
          '#nid' => $nid,
          '#class' => $i == 0 ? 'active' : '',
          '#image' => $image,
        ];

        if ($this->getSetting('show_captions') == 'yes') {
          // Get the caption field.
          $caption_field = $node->get('field_caption');
          $caption = $caption_field->view();
          $caption['#label_display'] = 'hidden';

          // Add the caption for this slide.
          $elements['#slides'][$delta]['#caption'] = $caption;
        }

        if ($this->getSetting('show_indicators') == 'yes') {
          // Add an indicator for this slide.
          $elements['#indicators'][$delta] = [
            '#theme' => 'oms_gallery_indicator',
            '#id' => $id,
            '#slide' => $i,
            '#class' => $i == 0 ? 'active' : '',
          ];
        }

        // Move to the next slide.
        $i++;
      }
    }

    return $elements;
  }
}
