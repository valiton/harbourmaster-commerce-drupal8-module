<?php
namespace Drupal\hms_commerce\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\NumericItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;

abstract class DigtapFieldBase extends NumericItemBase {

  const MAX_LENGTH = 9;

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'size' => 'normal',
        ),
      ),
    );
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
  public function getConstraints() {
    $constraints = parent::getConstraints();
    $field_label = $this->getFieldDefinition()->getLabel();

    // Add a validation constraint for the integer to be positive.
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'value' => array(
        'Range' => array(
          'min' => 0,
          'minMessage' => t('%name: The item ID must be larger or equal to %min.', array(
            '%name' => $field_label,
            '%min' => 0,
          )),
        ),
        'Length' => array(
          'max' => static::MAX_LENGTH,
          'maxMessage' => t('%name: The item ID cannot have more than @max characters.', array(
            '%name' => $field_label,
            '@max' => static::MAX_LENGTH,
          )),
        )),
    ));
    return $constraints;
  }
}
