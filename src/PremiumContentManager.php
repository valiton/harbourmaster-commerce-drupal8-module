<?php

namespace Drupal\hms_commerce;

/**
 * PremiumContentManager drupal service class.
 */
class PremiumContentManager {

  private $entity;
  private $digtapSettings;
  private $premiumFields = [];
  private $premium = FALSE;
  private $premiumContentField;
  private $entitlementGroupName;
  private $hmsContentId;

  const ERROR_MESSAGE_WEIGHT = -500;

  /**
   * PremiumContentManager constructor.
   */
  function __construct($entity, $digtap_settings) {
    $this->entity = $entity;
    $this->digtapSettings = $digtap_settings;
    $this->entitlementGroupName = $this->digtapSettings->getSetting('entitlement_group_name');
    $this->hmsContentId = $this->entity->getEntityTypeId() . "Id" . $this->entity->id();
    $this->setPremiumFields();
    if (!empty($this->getPremiumFields())) {
      $this->premium = TRUE;
    }
  }

  private function setPremiumFields() {
    foreach($this->entity->getFields() as $field) {
      if ($field->getFieldDefinition()->getType() == 'premium_content') {
        $this->premiumContentField = $field;
        if (!$this->premiumContentField->isEmpty()) {
          $this->premiumFields = array_filter(
            $this->premiumContentField->getSetting('premium_fields'), function($i) {return !empty($i);}
          );
        }
        break;
      }
    }
  }

  public function encryptPremiumFields(&$build, $encrypter) {
    $this->addPremiumContentErrorMessage($build);
    $build['#attached']['library'][] = 'hms_commerce/premiumContent';

    foreach($this->premiumFields as $premium_field_name) {
      if (isset($build[$premium_field_name])) {
        $rendered_field = render($build[$premium_field_name]);
        if (!empty($rendered_field)) {
          $encrypted_content = $encrypter
            ->setHmsContentId($this->hmsContentId)
            ->setSecretKey($this->digtapSettings->getSetting('shared_secret_key'))
            ->encryptContent($rendered_field);
          $encrypted_field = [
            '#markup' => $this->addPremiumFieldMarkup($encrypted_content),
            '#weight' => $build[$premium_field_name]['#weight'],
          ];
          $build[$premium_field_name] = $encrypted_field;
        }
      }
    }
  }

  function addPremiumContentErrorMessage(&$build) {
    $message = $this->digtapSettings->getSetting('premium_content_error');
    if (!empty($message)) {
      $build['premium_content_error_message'] = [
        '#markup' => "<div class='hms-access-error'>" .  t($message) . "</div>",
        '#weight' => self::ERROR_MESSAGE_WEIGHT,
      ];
    }
  }

  private function addPremiumFieldMarkup($encrypted_string) {
    $entitlement_group_name = !empty($this->entitlementGroupName) ? $this->entitlementGroupName . " OR " : '';
    $output = "<div hms-access='"
      . $entitlement_group_name
      . $this->hmsContentId
      . "' hms-external-id='"
      . $this->hmsContentId
      . "'>"
      . $encrypted_string
      . "</div>";
    return $output;
  }

  public function addTeaserMarkup(&$build) {
    $teaser_field = $this->premiumContentField->getSetting('teaser_field');
    if (!empty($teaser_field) && isset($build[$teaser_field])) {
      $rendered_field = render($build[$teaser_field]);
      $entitlement_group_name = !empty($this->entitlementGroupName) ? $this->entitlementGroupName . " OR " : '';
      $rendered_teaser = [
        '#markup' => "<div hms-access='NOT("
          . $entitlement_group_name
          . $this->hmsContentId
          . ")'>"
          . $rendered_field
          . "</div>",
        '#weight' => $build[$teaser_field]['#weight'],
      ];
      $build[$teaser_field] = $rendered_teaser;
    }
  }

  public function isPremium() {
    return $this->premium;
  }

  public function getPremiumFields() {
    return $this->premiumFields;
  }
}
