<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\hms_commerce\Form\NewsletterForm;

/**
 * Class DigtapFormatterBase
 * @package Drupal\hms_commerce\Plugin\Field\FieldFormatter
 *
 * @todo Extend ContainerFactoryPluginInterface and inject services instead of calling \Drupal::service().
 */
abstract class DigtapFormatterBase extends FormatterBase {

  protected $widgetType = 'PremiumDownload';

  /**
   * {@inheritdoc}
   *
   * @todo Only show element if content is premium and user has no entitlement?
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    if ($items->count() > 0) {
      $product_ids = [];

      // Format field output and collect product ids for the JS script.
      $field_name = $items->getName();
      $field_dom_id = $field_name . '-' . $this->widgetType;
      foreach ($items as $delta => $item) {
        $elements[$delta] = [
          '#type' => 'markup',
          '#markup' => "<div id='" . $field_dom_id . '-' . $delta . "'></div>",
        ];
        $product_ids[$delta] = $item->value;
      }

      // Attach JS and its settings to field formatter.
      $settings = \Drupal::service('hms_commerce.settings');
      if (!empty($bestseller_url = $settings->getResourceUrl('bestseller'))) {
        $elements['#attached']['library'][] = 'hms_commerce/products';
        $elements['#attached']['drupalSettings']['hms_commerce']['bestseller_url'] = $bestseller_url;
        $elements['#attached']['drupalSettings']['hms_commerce']['formatter_settings'][$this->widgetType][$field_name] = [
          'field_dom_id' => $field_dom_id,
          'product_ids' => $product_ids,
        ];

        // Attach newsletter settings.
        $elements['#attached']['drupalSettings']['hms_commerce']['newsletter'] = [
          'client' => $settings->getSetting('newsletter_client_id'),
          'origin' => !empty($origin = $settings->getSetting('newsletter_origin')) ? $origin : NewsletterForm::getOrigin(),
          'contact_permission' => $settings->getSetting('show_contact_permission') ? 1 : 0,
          'privacy_permission' => $settings->getSetting('show_privacy_permission') ? 1 : 0,
        ];
        $newsletter_groups = $settings->getSetting('newsletter_groups');
        $elements['#attached']['drupalSettings']['hms_commerce']['newsletter']['newsletter_groups'] = $newsletter_groups;
        foreach($newsletter_groups as $group) {
          $elements['#attached']['drupalSettings']['hms_commerce']['newsletter']['newsletter_groups'][$group['id']] = $group['name'];
        }
      }
    }
    return $elements;
  }
}
