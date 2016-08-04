<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "digtap_product",
 *   label = @Translation("Bestseller product"),
 *   field_types = {
 *     "digtap_product"
 *   }
 * )
 */
class DigtapProductFormatter extends FormatterBase {

  const WIDGET_TYPE = 'PremiumDownload';

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $product_ids = [];

    // Format field output and collect product ids for the JS script.
    $field_id = $items->getName();
    $field_dom_id = $field_id . '-' . self::WIDGET_TYPE;
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'markup',
        '#markup' => "<div id='" . $field_dom_id . '-' . $delta . "'></div>",
      ];
      $product_ids[$delta] = $item->value;
    }

    // Attach JS and its settings to any page displaying this field.
    $bestseller_url = \Drupal::service('hms_commerce.settings')->getResourceUrl('bestseller');
    if (!empty($bestseller_url)) {
      $elements['#attached']['library'][] = 'hms_commerce/products';
      $elements['#attached']['drupalSettings']['hms_commerce']['bestseller_url'] = $bestseller_url;
      $elements['#attached']['drupalSettings']['hms_commerce']['widget_type'] = self::WIDGET_TYPE;
      $elements['#attached']['drupalSettings']['hms_commerce']['digtap_product_formatter_settings'][$field_id] = [
        'field_dom_id' => $field_dom_id,
        'product_ids' => $product_ids,
      ];
    }
    return $elements;
  }
}
