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
 *     "digtap_product"
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
        '#description' => $this->t(''),
        '#attributes' => ['class' => ['digtap-product-widget']]
      ];
    // Attach JS and its settings to any page displaying this field.
    $bestseller_url = \Drupal::service('hms_commerce.settings')->getApiUrl(TRUE);
    if (!empty($bestseller_url)) {
      $form['#attached']['library'][] = 'hms_commerce/digtapProductWidget';
      $form['#attached']['drupalSettings']['hms_commerce'] = [
        'bestseller_url' => $bestseller_url,
      ];
    }
    return $element;
  }
}
