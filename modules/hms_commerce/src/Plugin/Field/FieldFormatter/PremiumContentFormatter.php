<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\OptGroup;

/**
 * @FieldFormatter(
 *   id = "premium_content",
 *   label = @Translation("Price category"),
 *   field_types = {
 *     "premium_content"
 *   }
 * )
 */
class PremiumContentFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    // Only collect allowed options if there are actually items to display.
    if ($items->count()) {
      foreach ($items as $delta => $item) {
        $output = $item->value;
        $elements[$delta] = array(
          '#markup' => $output,
          '#allowed_tags' => FieldFilteredMarkup::allowedTags(),
        );
      }
    }
    return $elements;
  }
}
