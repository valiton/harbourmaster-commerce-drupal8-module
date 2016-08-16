<?php

namespace Drupal\hms_commerce\Tests\Plugin\Field;

/**
 * Tests premium_content field functional integration.
 *
 * @group hms_commerce
 */
class PremiumContentFieldTest extends DigtapFieldTestBase {

  protected $fieldEditPath;
  protected $manageFieldsPath;
  protected $entityViewPath;
  protected $entityEditPath;

  /**
   * Implements setup().
   */
  protected function setUp() {
    parent::setUp();
    $this->manageFieldsPath = "admin/structure/types/manage/$this->contentType/fields";
    $this->fieldEditPath = "admin/structure/types/manage/$this->contentType/fields/$this->entityType.$this->contentType.$this->fieldName";
    $this->entityViewPath = $this->entityType . '/' . $this->node->id();
    $this->entityEditPath = $this->entityViewPath . '/edit';
    $this->drupalLogin($this->privilegedUser);
  }

  protected function defineField() {
    $this->fieldType = 'premium_content';
    $this->fieldName = 'field_' . $this->fieldType;
    $this->fieldLabel = 'Premium content';
  }

  protected function setPremiumField() {
    $edit = ['settings[premium_fields][body]' => TRUE];
    $this->drupalPostForm($this->fieldEditPath, $edit, t('Save settings'));
  }

  protected function setTeaserField() {
    $edit = ['settings[teaser_fields][title]' => TRUE];
    $this->drupalPostForm($this->fieldEditPath, $edit, t('Save settings'));
  }

  protected function setEntityPremium() {
    $edit = [$this->fieldName . '[0][value]' => [1]];
    $this->drupalPostForm($this->entityEditPath, $edit, t('Save and keep published'));
  }

  public function testAddFieldToEntityType() {
    $this->drupalGet($this->manageFieldsPath);
    $this->assertText($this->fieldName);
  }

  public function testFieldSettingsPresent() {
    $this->drupalGet($this->fieldEditPath);
    $this->assertText('Premium fields');
    $this->assertText('Teaser fields');
  }

  public function testSetPremiumField() {
    $this->setPremiumField();
    $this->drupalGet($this->fieldEditPath);
    $this->assertFieldChecked('edit-settings-premium-fields-body');
  }

  public function testSetTeaserField() {
    $this->setTeaserField();
    $this->drupalGet($this->fieldEditPath);
    $this->assertFieldChecked('edit-settings-teaser-fields-title');
  }

//  public function testSetEntityPremium() {
//    $this->setEntityPremium();
//    $this->drupalGet($this->entityEditPath);
//    $this->assertOptionSelected($this->fieldName . '-0-value', '1');
//  }
}
