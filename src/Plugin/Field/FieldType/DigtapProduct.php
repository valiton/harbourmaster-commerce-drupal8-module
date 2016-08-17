<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

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
class DigtapProduct extends DigtapFieldBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('integer')
      ->setLabel(t('Product ID'));
    return $properties;
  }
}
