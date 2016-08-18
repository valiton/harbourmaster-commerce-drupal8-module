<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

abstract class DigtapFormatterBase extends FormatterBase {

  protected $widgetType = 'PremiumDownload';

  /**
   * {@inheritdoc}
   *
   * @todo Only show element if content is premium and user has no entitlement?
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    if ($items->count() > 0) {
      $product_ids = [];

      // Format field output and collect product ids for the JS script.
      $field_name = $items->getName();
      $field_dom_id = $field_name . '-' . $this->widgetType;
      foreach ($items as $delta => $item) {
        $elements[$delta] = [
          '#type' => 'markup',
          '#markup' => "<div id='" . $field_dom_id . '-' . $delta . "'></div>",
        ];
        $product_ids[$delta] = $item->value;
      }

      // Attach JS and its settings to field formatter.
      $bestseller_url = \Drupal::service('hms_commerce.settings')->getResourceUrl('bestseller');
      if (!empty($bestseller_url)) {
        $elements['#attached']['library'][] = 'hms_commerce/products';
        $elements['#attached']['drupalSettings']['hms_commerce']['bestseller_url'] = $bestseller_url;
        $elements['#attached']['drupalSettings']['hms_commerce']['formatter_settings'][$this->widgetType][$field_name] = [
          'field_dom_id' => $field_dom_id,
          'product_ids' => $product_ids,
        ];
      }
    }
    return $elements;
  }
}
