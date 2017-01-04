<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;

/**
 * @FieldWidget(
 *   id = "digtap_product",
 *   label = @Translation("Bestseller product"),
 *   field_types = {
 *     "digtap_product"
 *   }
 * )
 *
 * @todo Extend ContainerFactoryPluginInterface and inject services instead of calling \Drupal::service().
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
    $field_label = $items->getFieldDefinition()->getLabel();
    $dom_container_id = 'digtap-product-widget-' . $field_name;
    $dom_input_id = $dom_container_id . '-input';

    $element['products'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t($field_label),
    );

    $element['products']['product_store'] = [
      '#type' => 'hidden',
//      '#type' => 'textfield',

      // Put all values into one single hidden field.
      '#default_value' => implode(',', $values),

      // ID for digtap widget to be able to target this hidden field.
      '#attributes' => ['id' => [$dom_input_id]],
    ];

    $element['products']['product_container'] = [
      '#type' => 'markup',

      // Empty container for the digtap widget to use it to display products.
      '#markup' => "<div id='$dom_container_id'></div>",
    ];

    // Attach JS and its settings to this field widget.
    $bestseller_url = \Drupal::service('hms_commerce.settings')->getResourceUrl('bestseller');
    if (!empty($bestseller_url)) {
      $form['#attached']['library'][] = 'hms_commerce/digtapProductWidget';
      $form['#attached']['drupalSettings']['hms_commerce']['bestseller_url'] = $bestseller_url;
      $form['#attached']['drupalSettings']['hms_commerce']['digtap_product_widget_settings'][$field_name] = [
        'input_id' => $dom_input_id,
        'container_id' => $dom_container_id,
      ];
    }
    return [$element];
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
    $field_values = $form_state->getValues();

    // Using recursiveFind as field value might be inside the paragraph field.
    $field_value = $this->recursiveFind($field_values, $this->fieldDefinition->getName());

    if (isset($field_value[0]['products']['product_store'])) {
      $values = explode(',', $field_value[0]['products']['product_store']);
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
    else {
      //todo exception
    }
  }

  /**
   * Searches an array recursively for a key and returns the value of the first key found.
   *
   * @param array $array
   * @param $needle
   * @return mixed
   */
  private function recursiveFind(array $array, $needle) {
    $recursive = new RecursiveIteratorIterator(
      new RecursiveArrayIterator($array),
      RecursiveIteratorIterator::SELF_FIRST);
    foreach ($recursive as $key => $value) {
      if ($key === $needle) {
        return $value;
      }
    }
    return FALSE;
  }
}

