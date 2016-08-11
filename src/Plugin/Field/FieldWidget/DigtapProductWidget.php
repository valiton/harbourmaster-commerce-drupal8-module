<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "digtap_product",
 *   label = @Translation("Bestseller product"),
 *   field_types = {
 *     "digtap_product"
 *   }
 * )
 */
class DigtapProductWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   *
   * Replacing stock Drupal multiple elements widget with one hidden form field
   * which will hold a concatenated string of all values.
   */
  protected function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state) {
    $values = [];
    foreach($items as $item) {
      $values[] = $item->value;
    }
    $field_name = $items->getName();
    $dom_container_id = 'digtap-product-widget-' . $field_name;
    $dom_input_id = $dom_container_id . '-input';
    $hidden_field = [
      '#type' => 'hidden',
      '#default_value' => implode(',', $values),
      '#attributes' => ['id' => [$dom_input_id]], // ID for digtap widget to be able to target this hidden field.
      '#suffix' => "<div id='$dom_container_id'></div>", // Empty container for the digtap widget to use it to display products.
    ];

    // Attach behaviour to display/hide the select field dynamically
    // Attach JS and its settings to any page displaying this field.
    $bestseller_url = \Drupal::service('hms_commerce.settings')->getResourceUrl('bestseller');
    if (!empty($bestseller_url)) {
      $form['#attached']['library'][] = 'hms_commerce/digtapProductWidget';
      $form['#attached']['drupalSettings']['hms_commerce']['bestseller_url'] = $bestseller_url;
      $form['#attached']['drupalSettings']['hms_commerce']['digtap_product_widget_settings'][$field_name]['input_id'] = $dom_input_id;
      $form['#attached']['drupalSettings']['hms_commerce']['digtap_product_widget_settings'][$field_name]['container_id'] = $dom_container_id;
    }
    return [$hidden_field];
  }

  /**
   * {@inheritdoc}
   *
   * Stopping Drupal from creating a form element for each value.
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   *
   * Extracting all values from the concatenated string so Drupal can run
   * validation on each value and save them separately.
   */
  public function extractFormValues(FieldItemListInterface $items, array $form, FormStateInterface $form_state) {
    $value_string = $form_state->getValue($this->fieldDefinition->getName());
    $values = explode(',', $value_string[0]);
    foreach($values as $i => $value) {
      $values[$i] = [
        'value' => $value,
        '_weight' => $i,
      ];
    }
    // Let the widget massage the submitted values.
    $values = $this->massageFormValues($values, $form, $form_state);

    // Assign the values and remove the empty ones.
    $items->setValue($values);
  }
}
