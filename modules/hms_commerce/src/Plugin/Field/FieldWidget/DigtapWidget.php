<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "digtap_widget",
 *   label = @Translation("Digtap widget"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */
class DigtapWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = $element + array(
        '#type' => 'textfield',
        '#title' => $this->t('Select product'),
        '#default_value' => (isset($items[$delta]->value)) ? $items[$delta]->value : '',
        '#description' => t(''),
      );
    return $element;
  }
}
