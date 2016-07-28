<?php

namespace Drupal\hms_commerce;

/**
 * PremiumContentManager drupal service class.
 */
class PremiumContentManager {

  private $entity;
  private $premiumFields = [];
  private $premium = FALSE;
  private $premiumContentField;
  private $entitlementGroupName;

  /**
   * PremiumContentManager constructor.
   */
  function __construct($entity) {
    $this->entity = $entity;
    $this->setPremiumFields();
  }

  private function setPremiumFields() {
    foreach($this->entity->getFields() as $field) {
      if ($field->getFieldDefinition()->getType() == 'premium_content') {
        $this->premiumContentField = $field;
        if (!empty($this->premiumContentField->getValue())) {
          $this->premium = TRUE;
          $this->premiumFields = array_filter($this->premiumContentField->getSetting('premium_fields'), function($i) {return !empty($i);});
        }
        break;
      }
    }
  }

  public function setEntitlementGroupName($name) {
    $this->entitlementGroupName = $name;
  }

  public function encryptPremiumFields(&$build, $encrypter) {
    $build['#attached']['library'][] = 'hms_commerce/premiumContent';
    foreach($this->premiumFields as $premium_field_name) {
      if (isset($build[$premium_field_name])) {
        $rendered_field = render($build[$premium_field_name]);
        if (!empty($rendered_field)) {
          $encrypted_field = [
            '#markup' => $this->addPremiumFieldMarkup($encrypter->encryptContent($this->entity->id(), $rendered_field)),
            '#weight' => $build[$premium_field_name]['#weight'],
          ];
          $build[$premium_field_name] = $encrypted_field;
        }
      }
    }
  }

  public function showTeaser(&$build) {
    $teaser_field = $this->premiumContentField->getSetting('teaser_field');
    if (!empty($teaser_field) && isset($build[$teaser_field])) {
      //todo
    }
  }

  public function isPremium() {
    return $this->premium;
  }

  public function getPremiumFields() {
    return $this->premiumFields;
  }

  /**
   * @return array
   *  Array with category IDs as indexes and prices as values.
   *
   * @todo Either use curl or implement hook_requirements to check for allow_url_fopen.
   * @todo Adjust method to API which is to change.
   */
  public static function getPriceCategories() {
    $categories = [];
    $settings = \Drupal::service('hms_commerce.settings');
    $url = $settings->getResourceUrl('price_category');
    if (!empty($url)) {
      $response = json_decode(file_get_contents($url));
      if (isset($response->products)) {
        foreach($response->products as $category) {
          $categories[$category->product_id] = $category->product;
        }
      }
      else {
        $settings::registerError('There was a problem connecting to the API: Either the service is down, or an incorrect URL is set in the module settings.', 'error');
      }
    }
    return $categories;
  }
}
