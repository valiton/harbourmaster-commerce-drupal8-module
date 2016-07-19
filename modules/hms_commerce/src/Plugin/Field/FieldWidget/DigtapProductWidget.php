<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "digtap_product",
 *   label = @Translation("Digtap product"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */
class DigtapProductWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = $element + [
        '#type' => 'textfield',
        '#title' => $this->t('Select product'),
        '#default_value' => (isset($items[$delta]->value)) ? $items[$delta]->value : '',
        '#description' => t(''),
        '#attributes' => ['class' => ['digtap-product-widget']]
      ];
    // Attach behaviour to field dynamically.
    // Attach JS and its settings to any page displaying this field.
    $api_source = \Drupal::service('hms_commerce.settings')->getApiUrl(TRUE);
    if (!empty($api_source)) {
      $form['#attached']['library'][] = 'hms_commerce/digtapProductWidget';
      $form['#attached']['drupalSettings']['hms_commerce'] = [
        'api_source' => $api_source,
      ];
    }
    return $element;
  }
}
