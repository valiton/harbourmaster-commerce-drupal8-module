<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\Plugin\Field\FieldType\NumericItemBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'digtap_product' field type.
 *
 * @FieldType(
 *   id = "digtap_product",
 *   label = @Translation("Bestseller product"),
 *   description = @Translation("This field stores and displays Bestseller products."),
 *   category = @Translation("Reference"),
 *   default_widget = "digtap_product",
 *   default_formatter = "digtap_product"
 * )
 */
class DigtapProduct extends NumericItemBase {

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('integer')
      ->setLabel(t('Product ID'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    // Add a validation constraint for the integer to be positive.
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'value' => array(
        'Range' => array(
          'min' => 0,
          'minMessage' => t('%name: The product ID must be larger or equal to %min.', array(
            '%name' => $this->getFieldDefinition()->getLabel(),
            '%min' => 0,
          )),
        ),
      ),
    ));
    return $constraints;
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
}
