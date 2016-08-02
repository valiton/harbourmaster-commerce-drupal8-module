<?php

namespace Drupal\Tests\hms_commerce\Unit;

/**
 * @coversDefaultClass \Drupal\hms_commerce\Digtap
 * @group hms_commerce
 */
class HmscommerceSettingsTest extends HmscommerceSettingsTestBase {

  protected $digtap;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Test setting and getting of base URL.
   */
  public function testGetSettings() {
    $this->assertEquals($this->digtap->getSetting('bestseller_url'), $this->config['bestseller_url']);
  }
}
