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
 *
 * @todo Extend ContainerFactoryPluginInterface and inject services instead of calling \Drupal::service().
 */
class PremiumContentWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $default_value = (isset($items[$delta]->value)) ? $items[$delta]->value : NULL;

    $element['premium'] = [
      '#type' => 'checkbox',
      '#title' => $this->t($items->getFieldDefinition()->getLabel()),
      '#default_value' => !is_null($default_value),
//      '#element_validate' => [[$this, 'validatePremium']],
    ];

    $price_categories = \Drupal::service('hms_commerce.settings')->getPriceCategories();
    $element['value'] = [
      '#type' => 'select',

      // Make sure the dropdown shows option '- Current -' when there is no connection to Bestseller API
      // or if the category ID vanished from it. This is to make sure that the value is kept after clicking 'Save'.
      '#options' => (!isset($price_categories[$default_value]) ? [$default_value => $this->t('- Current (connection error) -')] : [])
        + $price_categories,

      '#empty_value' => '',
      '#default_value' => $default_value,
      '#title' => $this->t('Price category'),
      '#element_validate' => [[$this, 'validatePriceCategory']],
      ];

    // Attach behaviour to display/hide the select field dynamically
    $field_name = $items->getName();
    $form['#attached']['library'][] = 'hms_commerce/premiumContentWidget';
    $form['#attached']['drupalSettings']['hms_commerce']['premium_content_widget_settings'][$field_name] = 'field--name-'
      . str_replace('_', '-', $field_name);
    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * @param $element
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @todo Unset value in $element['value'] if checkbox is empty instead of current JS solution? Use $form_state->setValueForElement($element, '');
   */
  public function validatePremium($element, FormStateInterface $form_state) {
    if (empty($element['#value'])) {
      $form = $form_state->getCompleteForm();
    }
  }

  /**
   * {@inheritdoc}
   *
   * @param $element
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @todo Allow setting a price category (making content premium) only if at least one field is set premium in this field's settings.
   */
  public function validatePriceCategory($element, FormStateInterface $form_state) {
  }
}
