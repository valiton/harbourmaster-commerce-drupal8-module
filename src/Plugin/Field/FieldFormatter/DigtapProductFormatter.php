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
    foreach ($items as $delta => $item) {
      $elements[$delta] = array(
        '#type' => 'markup',
        '#markup' => "<div id='digtap-widget-" . self::WIDGET_TYPE
          . '-' . $delta . "'></div>",
      );
      $product_ids[$delta] = $item->value;
    }

    // Attach JS and its settings to any page displaying this field.
    $bestseller_url = \Drupal::service('hms_commerce.settings')->getResourceUrl(TRUE);
    if (!empty($bestseller_url)) {
      $elements['#attached']['library'][] = 'hms_commerce/products';
      $elements['#attached']['drupalSettings']['hms_commerce'] = [
        'bestseller_url' => $bestseller_url,
        'widget_type' => self::WIDGET_TYPE,
        'product_ids' => $product_ids,
      ];
    }
    return $elements;
  }
}
