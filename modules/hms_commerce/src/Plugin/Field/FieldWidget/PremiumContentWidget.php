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
        '#options' => $this->getPriceCategories(),
        '#empty_value' => '',
        '#default_value' => $default_value,
        '#description' => $this->t('Price category'),
        '#prefix' => $this->renderPremiumCheckbox(!is_null($default_value)),
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

  /**
   * @return array
   *  Array with category IDs as indexes and prices as values.
   *
   * @todo Either use curl or implement hook_requirements to check for allow_url_fopen.
   * @todo Adjust method to API which is to change.
   */
  private function getPriceCategories() {
    $categories = [];
    $settings = \Drupal::service('hms_commerce.settings');
    $url = $settings->getApiUrl('price_category');
    if (!empty($url)) {
      $response = json_decode(file_get_contents($url));
      if (isset($response->products)) {
        foreach($response->products as $category) {
          $categories[$category->product_id] = $category->product;
        }
      }
      else {
        $settings::registerError('There was a problem connecting to the API: Either the service is down, or an incorrect URL is set in the module settings.', [], 'error');
      }
    }
    return $categories;
  }
}
