<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "digtap_formatter",
 *   label = @Translation("Digtap widget"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */
class DigtapFormatter extends FormatterBase {

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
        '#markup' => "<div id='digtap-widget-" . $item->value . "'></div>",
      );
      $product_ids[] = $item->value;
    }

    // Attach JS and its settings to any page displaying this field.
    $api_source = \Drupal::config('hms_commerce.settings')->get('api_source');
    if (!empty($api_source)) {
      $elements['#attached']['library'][] = 'hms_commerce/products';
      $elements['#attached']['drupalSettings']['hms_commerce']['api_source'] = $api_source;
      $elements['#attached']['drupalSettings']['hms_commerce']['product_ids'] = $product_ids;
    }

    // Warn administrative user if the API URL is not set.
    elseif (\Drupal::currentUser()->hasPermission('administer hms_commerce settings')) {
      drupal_set_message(t("For products to display, the API source URL needs to be set <a href='@url'>here</a>.", [
        '@url' => $GLOBALS['base_url'] . "/admin/config/hmscommerce"]), 'warning');
    }
    return $elements;
  }
}
