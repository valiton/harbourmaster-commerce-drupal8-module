<?php

namespace Drupal\hms_commerce\Tests\Plugin\Field;

/**
 * Tests digtap_product field functional integration.
 *
 * @group hms_commerce
 */
class  DigtapProductFieldTest extends DigtapFieldTestBase {

  protected function defineField() {
    $this->fieldType = 'digtap_product';
    $this->fieldName = 'field_' . $this->fieldType;
    $this->fieldLabel = 'Products';
  }

  public function testAddFieldToEntityType() {
    $this->drupalLogin($this->privilegedUser);
    $this->drupalGet('admin/structure/types/manage/' . $this->contentType . '/fields');
    $this->assertText($this->fieldName);
  }
}
