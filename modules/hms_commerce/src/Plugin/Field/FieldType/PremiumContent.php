<?php
namespace Drupal\hms_commerce\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Plugin implementation of the 'country' field type.
 *
 * @FieldType(
 *   id = "premium_content",
 *   label = @Translation("Premium content"),
 *   description = @Translation("Stores the price category of a premium content piece."),
 *   category = @Translation("Thunder"),
 *   default_widget = "premium_content",
 * )
 */
class PremiumContent extends FieldItemBase {

//  const CATEGORY_ID_MAX_LENGTH = 20;

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Price category ID'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'description' => t('Price category ID.'),
          'type' => 'int',
          'unsigned' => TRUE,
        ),
      ),
      'indexes' => array(
        'value' => array('value'),
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
   *
   * @todo: Add integer constraint, in case category IDS coming from the cloud are erroneous.
   */
//  public function getConstraints() {
//    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
//    $constraints = parent::getConstraints();
//    $constraints[] = $constraint_manager->create('ComplexData', array(
//      'value' => array(
//        'Length' => array(
//          'max' => self::CATEGORY_ID_MAX_LENGTH,
//          'maxMessage' => t('%name: The price category ID may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => self::CATEGORY_ID_MAX_LENGTH)),
//        )
//      ),
//    ));
//    return $constraints;
//  }
}
