<?php

namespace Drupal\website_zip_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'website_zip_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "website_zip_formatter",
 *   label = @Translation("Website ZIP print link"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class WebsiteZipFormatter extends FormatterBase {

  public static function defaultSettings() {
    return [
      'link_text' => 'Print website',
    ] + parent::defaultSettings();
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];
    $elements['link_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link text'),
      '#default_value' => $this->getSetting('link_text'),
    ];
    return $elements;
  }

  public function settingsSummary() {
    return [
      $this->t('Link text: @text', ['@text' => $this->getSetting('link_text')]),
    ];
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $file = NULL;
      if ($item->entity instanceof File) {
        $file = $item->entity;
      }
      elseif (!empty($item->target_id)) {
        $file = File::load($item->target_id);
      }

      if ($file) {
        $url = Url::fromRoute('website_zip_formatter.print', ['file' => $file->id(), 'path' => '/']);
        $text = $this->getSetting('link_text') ?: $item->description ?: $file->getFilename();
        $link = Link::fromTextAndUrl($text, $url);
        $elements[$delta] = $link->toRenderable();
      }
      else {
        $elements[$delta] = ['#markup' => $this->t('File not available')];
      }
    }
    return $elements;
  }

}
