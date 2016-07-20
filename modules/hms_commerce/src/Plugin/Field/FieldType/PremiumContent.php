<?php
namespace Drupal\hms_commerce\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Plugin implementation of the 'premium_content' field type.
 *
 * @FieldType(
 *   id = "premium_content",
 *   label = @Translation("Premium content"),
 *   description = @Translation("Stores the price category of a premium content piece."),
 *   category = @Translation("Thunder"),
 *   default_widget = "premium_content",
 *   default_formatter = "list_default"
 * )
 *
 * @todo: Add new formatter and change default_formatter value.
 */
class PremiumContent extends FieldItemBase {

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
}
