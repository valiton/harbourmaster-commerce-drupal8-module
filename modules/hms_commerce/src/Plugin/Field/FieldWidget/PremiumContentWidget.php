<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "premium_content",
 *   label = @Translation("Premium content"),
 *   field_types = {
 *     "premium_content"
 *   }
 * )
 */
class PremiumContentWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $default_value = (isset($items[$delta]->value)) ? $items[$delta]->value : NULL;
    $element['value'] = $element + [
        '#type' => 'select',
        '#options' => [0 => 'test', 1 => 'test2'], //todo: API call must be implemented first
        '#empty_value' => '',
        '#default_value' => $default_value,
        '#description' => t('Price category'),
        '#prefix' => $this->renderPremiumCheckbox(!is_null($default_value)), //todo: remove prefix from field edit form
      ];
    // Attach behaviour to display/hide the select field dynamically.
    $form['#attached']['library'][] = 'hms_commerce/premiumContentWidget';

    return $element;
  }

  private function renderPremiumCheckbox($checked) {
    $checkbox = [
      '#type' => 'checkbox',
      '#title' => $this->t('Premium content'),
      '#checked' => $checked ? 'checked' : '',
    ];
    return render($checkbox);
  }
}
