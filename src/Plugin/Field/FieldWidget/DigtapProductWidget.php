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

    $hidden_field = [
//      '#type' => 'hidden', //todo: uncomment
      '#type' => 'textfield', //todo: remove

      '#default_value' => implode(',', $values),
      '#attributes' => ['class' => ['digtap-product-widget']],
    ];
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
