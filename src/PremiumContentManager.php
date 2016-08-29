<?php

namespace Drupal\hms_commerce;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * PremiumContentManager drupal service class.
 */
class PremiumContentManager {

  use StringTranslationTrait;

  private $entity;

  private $build;

  private $digtapSettings;

  /**
   * Encrypter object.
   *
   * @var object
   */
  private $encrypter;

  /**
   * Entity fields that have been marked premium on the premium_content field
   * settings page.
   *
   * @var array
   */
  private $premiumFields = [];

  /**
   * Entity fields that have been marked as teasers on the premium_content field
   * settings page.
   *
   * @var array
   */
  private $teaserFields = [];

  /**
   * @var bool
   */
  private $premiumCapable = FALSE;

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
  function __construct($digtap_config, $encrypter) {
    $this->digtapSettings = $digtap_config;
    $this->encrypter = $encrypter;
    $this->entitlementGroupName = $this->digtapSettings->getSetting('entitlement_group_name');
  }

  /**
   * Sets the entity object to be processed.
   *
   * @param $entity
   *
   * @return $this
   */
  public function setEntity($entity) {
    $this->entity = $entity;
    $this->setHmsContentId();
    return $this;
  }

  /**
   * @param $build
   * @return $this
   */
  public function setBuild(&$build) {
    $this->build = &$build;
    return $this;
  }

  public function process() {
    if ($this->setPremiumContentField()) {
      $this->premiumCapable = TRUE;
      $this->premium = !$this->premiumContentField->isEmpty();
      $this->setTeaserFields();
      if ($this->entityIsPremium() || !empty($this->getTeaserFields())) {
        $this->build['#attached']['library'][] = 'hms_commerce/premiumContent';
        $this->addTeaserMarkup();
      }
      if ($this->entityIsPremium()) {
        $this->addPremiumContentErrorMessage();
        $this->setPremiumFields();
        $this->encryptPremiumFields();
        $this->addPremiumContentFieldMarkup();
      }
    }
  }

  /**
   * Checks if this entity includes premium content.
   *
   * @return bool
   */
  public function entityIsPremium() {
    return $this->premium;
  }

  /**
   * @return bool
   */
  public function entityIsPremiumCapable() {
    return $this->premiumCapable;
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

  /**
   * Returns an array of this entity's teaser fields as defined in the
   * premium_content field's settings.
   *
   * @return array
   */
  public function getTeaserFields() {
    return $this->teaserFields;
  }

  /**
   * Sets the HMS content id from entity data.
   */
  private function setHmsContentId() {
    $this->hmsContentId = $this->entity->getEntityTypeId() . "Id" . $this->entity->id();
  }

  /**
   * Looks for the premium content field on the entity.
   *
   * @todo Only works with the first premium content field found. Decide whether alter this method or disallow multiple premium content fields.
   */
  private function setPremiumContentField() {
    foreach($this->entity->getFields() as $field) {
      if ($field->getFieldDefinition()->getType() == 'premium_content') {
        $this->premiumContentField = $field;
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Gathers all premium fields from this entity defined by the premium_content
   * field.
   */
  private function setPremiumFields() {
    $this->premiumFields = array_filter(
      $this->premiumContentField->getSetting('premium_fields'), function($i) {return !empty($i);}
    );
  }

  /**
   * Gathers all teaser fields from this entity defined by the premium_content
   * field.
   */
  private function setTeaserFields() {
    $this->teaserFields = array_filter(
      $this->premiumContentField->getSetting('teaser_fields'), function($i) {return !empty($i);}
    );
  }

  /**
   * Encrypts the premium bits of an entity's render array with an encrypter.
   * Should be called in hook_entity_view_alter.
   *
   * @return $this
   */
  private function encryptPremiumFields() {
    $this->encrypter
      ->setHmsContentId($this->hmsContentId)
      ->setSecretKey($this->digtapSettings->getSetting('shared_secret_key'));
    foreach($this->premiumFields as $premium_field_name) {
      $this->encryptField($premium_field_name, $this->build);
    }
  }

  /**
   * Encrypts a field.
   *
   * @param $field_name
   *
   * @return bool
   *  FALSE if field not in $build array, otherwise TRUE.
   */
  private function encryptField($field_name) {
    if (isset($this->build[$field_name])) {
      $rendered_field = render($this->build[$field_name]);
      if (!empty($rendered_field)) {
        $encrypted_content = $this->encrypter->encryptContent($rendered_field);
        $encrypted_field = [
          '#markup' => $this->addShowToEntitledMarkup($encrypted_content),
          '#weight' => $this->build[$field_name]['#weight'],
        ];
        $this->build[$field_name] = $encrypted_field;
      }
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Adds a generic error message near the top of the rendered entity.
   * Message is hidden via CSS and triggered via JS if needed.
   */
  private function addPremiumContentErrorMessage() {
    $message = $this->digtapSettings->getSetting('premium_content_error');
    if (!empty($message)) {
      $this->build['premium_content_error_message'] = [
        '#markup' => "<div class='hms-access-error'>" .  $this->t($message) . "</div>",
        '#weight' => self::ERROR_MESSAGE_WEIGHT,
      ];
    }
  }

  /**
   * Adds some HMS specific markup to a field string.
   *
   * @return string
   */
  private function addPremiumContentFieldMarkup() {
    $field_name = $this->premiumContentField->getName();
    if (isset($this->build[$field_name])) {
      $rendered_field = [
        '#markup' => $this->addShowToNotEntitledMarkup(render($this->build[$field_name])),
        '#weight' => $this->build[$field_name]['#weight'],
      ];
      $this->build[$field_name] = $rendered_field;
    }
  }

  /**
   * Adds HMS specific markup to teaser fields if such fields are defined in the
   * premium_field's settings.
   *
   * @return $this
   */
  private function addTeaserMarkup() {
    foreach($this->teaserFields as $teaser_field_name) {
      if (isset($this->build[$teaser_field_name])) {
        $rendered_field = [
          '#markup' => $this->addShowToNotEntitledMarkup(render($this->build[$teaser_field_name])),
          '#weight' => $this->build[$teaser_field_name]['#weight'],
        ];
        $this->build[$teaser_field_name] = $rendered_field;
      }
    }
  }

  /**
   * Adds some HMS specific markup to a (encrypted) field string.
   *
   * @param $string
   *
   * @return string
   */
  private function addShowToEntitledMarkup($string) {
    $entitlement_group_name = !empty($this->entitlementGroupName) ? $this->entitlementGroupName . " OR " : '';
    $output = "<div hms-access='"
      . $entitlement_group_name
      . $this->hmsContentId
      . "' hms-external-id='"
      . $this->hmsContentId
      . "'>"
      . $string
      . "</div>";
    return $output;
  }

  private function addShowToNotEntitledMarkup($string) {
    $entitlement_group_name = !empty($this->entitlementGroupName) ? $this->entitlementGroupName . " OR " : '';
    return "<div hms-access='NOT("
    . $entitlement_group_name
    . $this->hmsContentId
    . ")'>"
    . $string
    . "</div>";
  }
}
