<?php
namespace Drupal\hms_commerce\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use \Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'premium_content' field type.
 *
 * @FieldType(
 *   id = "premium_content",
 *   label = @Translation("Premium content"),
 *   description = @Translation("Stores the price category of a premium content piece."),
 *   category = @Translation("Thunder"),
 *   default_widget = "premium_content",
 *   default_formatter = "premium_content",
 * )
 *
 * @todo: Add new formatter and change default_formatter value.
 */
class PremiumContent extends FieldItemBase {

  public static function defaultFieldSettings() {
    return [
      'premium_fields' => [],
      'teaser_field' => [],
    ];/* + parent::defaultFieldSettings();*/
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
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'description' => t('Price category ID.'),
          'type' => 'int',
          'unsigned' => TRUE,
        ],
      ],
      'indexes' => ['value' => ['value']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    // Premium field settings
    $premium_field = $form_state->getFormObject()->getEntity();
    $bundle_fields = \Drupal::entityManager()
      ->getFieldDefinitions($premium_field->getTargetEntityTypeId(), $premium_field->getTargetBundle());
    $options = [];
    foreach($bundle_fields as $bundle_field) {
      if ($bundle_field->getType() != 'premium_content') { // Do not include premium_content field itself.
        $options[$bundle_field->getName()] = $bundle_field->getLabel();
      }
    }
    $element['premium_fields'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Premium fields'),
      '#description' => $this->t('The content of these fields will be marked premium and will be encrypted.'),
      '#default_value' => $this->getSetting('premium_fields'),
      '#options' => $options,
    ];

//    dpm($options);

    // Teaser field settings
    $element['teaser_field'] = [
      '#type' => 'radios',
      '#title' => $this->t('Teaser field'),
      '#description' => $this->t('Set a field to act as a teaser for the content from the premium fields.'),
      '#default_value' => $this->getSetting('teaser_field'),
      '#options' => [0 => t('(No teaser)')] + $options,
    ];
    return $element;
  }
}
