<?php

namespace Drupal\hms_commerce\Tests\Plugin\Field;

use Drupal\simpletest\WebTestBase;

/**
 * Test Base for hms_commerce field functional integration.
 *
 * @group hms_commerce
 */
class DigtapFieldTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['hms_commerce', 'node', 'field_ui'];

  protected $node;
  protected $privilegedUser;

  protected $fieldType;
  protected $fieldName;
  protected $fieldLabel;

  protected $entityType;
  protected $contentType;
  protected $entityTitle;

  /**
   * Implements setup().
   */
  protected function setUp() {
    parent::setUp();

    $perms = array_keys(\Drupal::service('user.permissions')->getPermissions());
    $this->privilegedUser = $this->drupalCreateUser($perms);

    $this->defineContent();
    $this->defineField();

    $this->drupalCreateContentType(['type' => $this->contentType]);

    $this->node = $this->createNode(['title' => $this->entityTitle, 'type' => $this->contentType]);

    $this->addFieldToEntityType();
  }

  protected function defineContent() {
    $this->entityType = 'node';
    $this->contentType = 'page';
    $this->entityTitle = 'Some title';
  }

  protected function defineField() {
  }

  protected function addFieldToEntityType() {
    \Drupal::entityManager()->getStorage('field_storage_config')->create([
      'field_name' => $this->fieldName,
      'entity_type' => $this->entityType,
      'type' => $this->fieldType,
      'translatable' => FALSE,
    ])->save();

    \Drupal::entityManager()->getStorage('field_config')->create([
      'field_name' => $this->fieldName,
      'entity_type' => $this->entityType,
      'bundle' => $this->contentType,
      'label' => $this->fieldLabel,
      'translatable' => FALSE,
    ])->save();
  }
}
