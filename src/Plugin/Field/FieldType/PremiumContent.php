<?php
namespace Drupal\hms_commerce\Plugin\Field\FieldType;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use \Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'premium_content' field type.
 *
 * @FieldType(
 *   id = "premium_content",
 *   label = @Translation("Premium content"),
 *   description = @Translation("Defines a premium content piece by allowing to set what fields to encrypt and add a bestseller price to it."),
 *   category = @Translation("Reference"),
 *   default_widget = "premium_content",
 *   default_formatter = "premium_content",
 * )
 *
 * @todo Extend ContainerFactoryPluginInterface and inject services instead of calling \Drupal::service().
 */
class PremiumContent extends DigtapFieldBase {

  public static function defaultFieldSettings() {
    return [
      'premium_fields' => [],
      'teaser_fields' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('integer')
      ->setLabel(t('Price category ID'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];
    $premium_field = $form_state->getFormObject()->getEntity();
    $bundle_fields = \Drupal::entityManager()
      ->getFieldDefinitions($premium_field->getTargetEntityTypeId(), $premium_field->getTargetBundle());
    $options = [];
    foreach ($bundle_fields as $bundle_field) {
      if ($bundle_field->getType() != 'premium_content') { // Do not include premium_content field itself.
        $options[$bundle_field->getName()] = $bundle_field->getLabel();
      }
    }

    // Premium field settings
    $element['premium_fields'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Premium fields'),
      '#description' => $this->t('The content of these fields will be marked premium and will be encrypted.'),
      '#default_value' => $this->getSetting('premium_fields'),
      '#options' => $options,
    ];

    // Teaser field settings
    $element['teaser_fields'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Teaser fields'),
      '#description' => $this->t('Set fields to act as teasers for the content from the premium fields.'),
      '#default_value' => $this->getSetting('teaser_fields'),
      '#options' => $options,
    ];

    return $element;
  }
}
