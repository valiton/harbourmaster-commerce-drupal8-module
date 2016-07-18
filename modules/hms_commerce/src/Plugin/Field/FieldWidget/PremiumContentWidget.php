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
        '#prefix' => $this->renderPremiumCheckbox(!empty($default_value)), //todo: remove prefix from field edit form
      ];
    return $element;
  }

  private function renderPremiumCheckbox($checked) {
    $checkbox = [
      '#type' => 'checkbox',
      '#title' => $this->t('Premium content'),
      '#default_value' => $checked,
    ];
    return render($checkbox);
  }
}
