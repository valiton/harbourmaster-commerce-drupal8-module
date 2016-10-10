<?php

namespace Drupal\Tests\hms_commerce\Unit;

/**
 * @coversDefaultClass \Drupal\hms_commerce\Digtap
 * @group hms_commerce
 */
class DigtapTest extends DigtapTestBase {

  /**
   * For tests to pass, this URL must point to a live instance of Bestseller.
   */
  const CURRENT_BESTSELLER_API_URL = 'https://digtap-bestseller-staging.sso-service.de';

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Test getting a setting.
   */
  public function testGetSetting() {
    $this->assertEquals(\Drupal::service('hms_commerce.settings')->getSetting('bestseller_url'), $this->config['bestseller_url']);
  }

  /**
   * Test getting unwanted empty setting.
   */
  public function testGetSettingWhenSettingEmpty() {
    $this->config['bestseller_url'] = '';
    $this->mockDigtapService();
    $this->assertEmpty(\Drupal::service('hms_commerce.settings')->getSetting('bestseller_url', 'some error message'));
  }

  /**
   * Test getting a setting when it does not exist.
   */
  public function testGetSettingWhenSettingNotExists() {
    $this->assertEmpty(\Drupal::service('hms_commerce.settings')->getSetting('non-existing setting', 'some error message'));
  }

  /**
   * Test getting a resource URL.
   */
  public function testGetResourceUrl() {
    $this->config['bestseller_url'] = self::CURRENT_BESTSELLER_API_URL;
    $this->mockDigtapService();
    $this->assertNotEmpty(\Drupal::service('hms_commerce.settings')->getResourceUrl('bestseller'));
  }

  /**
   * Test getting price categories from bestseller.
   */
  public function testGetPriceCategories() {
    $this->config['bestseller_url'] = self::CURRENT_BESTSELLER_API_URL;
    $this->mockDigtapService();
    $this->assertNotEmpty(\Drupal::service('hms_commerce.settings')->getPriceCategories());
  }
}
