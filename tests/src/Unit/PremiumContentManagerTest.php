<?php

namespace Drupal\Tests\hms_commerce\Unit;

/**
 * @coversDefaultClass \Drupal\hms_commerce\PremiumContentManager
 * @group hms_commerce
 *
 * @todo Add tests
 */
class PremiumContentManagerTest extends DigtapTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Some example test for the PremiumContentManager class.
   */
  public function testIsPremiumExample() {
    $this->mockPremiumContentManagerService();
    $this->assertFalse(\Drupal::service('hms_commerce.premium_content_manager')->isPremium());
  }
}
