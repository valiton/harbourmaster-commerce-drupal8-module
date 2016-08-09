<?php

namespace Drupal\hms_commerce;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * PremiumContentManager drupal class.
 */
class PremiumContentManager {

  use StringTranslationTrait;

  private $entity;

  private $digtapSettings;

  /**
   * Entity fields that have been marked premium on the premium_content field
   * settings page.
   *
   * @var array
   */
  private $premiumFields = [];

  /**
   * Indicates if the entity processed includes premium content.
   *
   * @var bool
   */
  private $premium = FALSE;

  /**
   * The premium_content field object defining whether and in which way an
   * entity is premium.
   *
   * @var object
   */
  private $premiumContentField;

  /**
   * Entitlement group name for this entity. Taken from hms_commerce's
   * configuration.
   *
   * @var string
   */
  private $entitlementGroupName;

  /**
   * An ID derived from the entity type name and the entity ID unique to this
   * Drupal installation and understood by HMS.
   *
   * @var string
   */
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

  /**
   * Gathers all premium fields from this entity defined by the premium_content
   * field.
   */
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

  /**
   * Encrypts the premium bits of an entity's render array with an encrypter.
   * Should be called in hook_entity_view_alter.
   *
   * @param $build
   * @param $encrypter
   */
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

  /**
   * Adds a generic error message near the top of the rendered entity.
   * Message is hidden via CSS and triggered via JS if needed.
   *
   * @param $build
   */
  private function addPremiumContentErrorMessage(&$build) {
    $message = $this->digtapSettings->getSetting('premium_content_error');
    if (!empty($message)) {
      $build['premium_content_error_message'] = [
        '#markup' => "<div class='hms-access-error'>" .  $this->t($message) . "</div>",
        '#weight' => self::ERROR_MESSAGE_WEIGHT,
      ];
    }
  }

  /**
   * Adds some HMS specific markup to an encrypted field string.
   *
   * @param $encrypted_string
   * @return string
   */
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

  /**
   * Adds HMS specific markup to the teaser field if such field is defined in
   * the premium_field's settings.
   *
   * @param $build
   */
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

  /**
   * Checks if this entity includes premium content.
   *
   * @return bool
   */
  public function isPremium() {
    return $this->premium;
  }

  /**
   * Returns an array of this entity's premium fields as defined in the
   * premium_content field's settings.
   *
   * @return array
   */
  public function getPremiumFields() {
    return $this->premiumFields;
  }
}
