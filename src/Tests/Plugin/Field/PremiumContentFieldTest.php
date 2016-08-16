<?php

namespace Drupal\hms_commerce\Tests\Plugin\Field;

/**
 * Tests premium_content field functional integration.
 *
 * @group hms_commerce
 */
class PremiumContentFieldTest extends DigtapFieldTestBase {

  protected function defineField() {
    $this->fieldType = 'premium_content';
    $this->fieldName = 'field_' . $this->fieldType;
    $this->fieldLabel = 'Premium content';
  }

  public function testAddFieldToEntityType() {
    $this->drupalLogin($this->privilegedUser);
    $this->drupalGet('admin/structure/types/manage/' . $this->contentType . '/fields');
    $this->assertText($this->fieldName);
  }
}
