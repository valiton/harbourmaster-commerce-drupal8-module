<?php

namespace Drupal\hms_commerce;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;

/**
 * PremiumContentManager drupal service class.
 */
class PremiumContentManager {

  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Entity\EntityInterface
   */
  private $entity;

  /**
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   */
  private $display;

  private $digtapSettings;

  /**
   * Encrypter object.
   *
   * @var object
   */
  private $encrypter;

  /**
   * All visible fields in the current view mode.
   *
   * @var array
   */
  private $visibleFields = [];

  /**
   * Visible entity fields that have been marked premium on the premium_content field
   * settings page.
   *
   * @var array
   */
  private $visiblePremiumFields = [];

  /**
   * Visible entity fields that have been marked as teasers on the premium_content field
   * settings page.
   *
   * @var array
   */
  private $visibleTeaserFields = [];

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
   * @param \Drupal\hms_commerce\Digtap $digtap_config
   * @param \Drupal\hms_commerce\Encrypter $encrypter
   */
  function __construct(Digtap $digtap_config, Encrypter $encrypter) {
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
  public function setEntity(EntityInterface $entity, EntityViewDisplayInterface $display) {
    $this->entity = $entity;
    $this->display = $display;
    return $this;
  }

  /**
   * @param $build
   */
  public function process(&$build) {

    if ($this->setPremiumContentField()) {

      $this->visibleFields = array_keys($this->display->getComponents());
      $this->setVisibleFields('premium');
      $this->setVisibleFields('teaser');

      if ($this->entityNeedsEncryption()) {

        $this->setHmsContentId();

        $build['#attached']['library'][] = 'hms_commerce/premiumContent';
        $build = $this->addTeaserMarkup($build);
        $build = $this->addPremiumContentErrorMessage($build);
        $build = $this->encryptPremiumFields($build);
        $build = $this->addPremiumContentFieldMarkup($build);
      }

      // If entity does not need encryption, remove teasers from render array.
      else {
        foreach ($this->visibleTeaserFields as $teaser_field_name) {
          unset($build[$teaser_field_name]);
        }
      }
    }
  }

  /**
   * Checks if this entity includes visible premium content.
   *
   * @return bool
   */
  public function entityNeedsEncryption() {
    return !$this->premiumContentField->isEmpty()
    && !empty($this->visiblePremiumFields)
    && !$this->digtapSettings->userHasSkipEncryptionPermission(TRUE);
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
   * Gathers all visible premium fields from this entity defined by the premium_content
   * field.
   *
   * @param $type
   */
  private function setVisibleFields($type) {
    foreach($this->premiumContentField->getSetting($type . '_fields') as $field_name => $setting) {
      if ($field_name === $setting && in_array($field_name, $this->visibleFields)) {
        $this->{'visible' . ucfirst($type) . 'Fields'}[] = $field_name;
      }
    }
  }

  /**
   * Encrypts all visible premium bits of an entity's render array with an encrypter.
   * Should be called in hook_entity_view_alter.
   *
   * @param $build
   * @return bool
   */
  private function encryptPremiumFields($build) {
    $this->encrypter
      ->setHmsContentId($this->hmsContentId)
      ->setSecretKey($this->digtapSettings->getSetting('shared_secret_key'));

    foreach($this->visiblePremiumFields as $premium_field_name) {
      if (isset($build[$premium_field_name])) {
        $weight = $build[$premium_field_name]['#weight'];
        $rendered_field = render($build[$premium_field_name]);
        if (!empty($rendered_field)) {
          $encrypted_content = $this->encrypter->encryptContent($rendered_field);
          $build[$premium_field_name] = [
            '#markup' => $this->addShowToEntitledMarkup($encrypted_content),
            '#weight' => $weight,
          ];
        }
      }
    }
    return $build;
  }

  /**
   * Adds a generic error message near the top of the rendered entity.
   * Message is hidden via CSS and triggered via JS if needed.
   *
   * @param $build
   * @return array
   */
  private function addPremiumContentErrorMessage($build) {
    $message = $this->digtapSettings->getSetting('premium_content_error');
    if (!empty($message)) {
      $build['premium_content_error_message'] = [
        '#markup' => "<div class='hms-access-error'>" .  $this->t($message) . "</div>",
        '#weight' => self::ERROR_MESSAGE_WEIGHT,
      ];
    }
    return $build;
  }

  /**
   * Adds some HMS specific markup to a field string.
   *
   * @param $build
   * @return array
   */
  private function addPremiumContentFieldMarkup($build) {
    $field_name = $this->premiumContentField->getName();
    if (isset($build[$field_name])) {
      $weight = $build[$field_name]['#weight'];
      $rendered_field = render($build[$field_name]);
      $build[$field_name] = [
        '#markup' => $this->addShowToNotEntitledMarkup($rendered_field),
        '#weight' => $weight,
      ];
    }
    return $build;
  }

  /**
   * Adds HMS specific markup to teaser fields if such fields are defined in the
   * premium_field's settings.
   *
   * @param $build
   * @return array
   */
  private function addTeaserMarkup($build) {
    foreach($this->visibleTeaserFields as $teaser_field_name) {
      if (isset($build[$teaser_field_name])) {
        $weight = $build[$teaser_field_name]['#weight'];
        $rendered_field = render($build[$teaser_field_name]);
        $build[$teaser_field_name] = [
          '#markup' => $this->addShowToNotEntitledMarkup($rendered_field),
          '#weight' => $weight,
        ];
      }
    }
    return $build;
  }

  /**
   * Adds some HMS specific markup to a (encrypted) field string.
   *
   * @param $string
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

  /**
   * @param $string
   * @return string
   */
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
