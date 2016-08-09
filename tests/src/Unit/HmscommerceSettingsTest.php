<?php

namespace Drupal\Tests\hms_commerce\Unit;

/**
 * @coversDefaultClass \Drupal\hms_commerce\Digtap
 * @group hms_commerce
 */
class HmscommerceSettingsTest extends HmscommerceSettingsTestBase {

  protected $digtap;

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
    $this->digtapMock->expects($this->never())->method('registerError');
    $this->assertEquals($this->digtap->getSetting('bestseller_url'), $this->config['bestseller_url']);
  }

  /**
   * Test getting unwanted empty setting.
   */
  public function testGetSettingWhenSettingEmpty() {
    $this->config['bestseller_url'] = '';
    $this->mockDigtapService();
    $this->digtapMock->expects($this->once())->method('registerError');
    $this->assertEmpty($this->digtap->getSetting('bestseller_url', 'some error message'));
  }

  /**
   * Test getting a setting when it does not exist.
   */
  public function testGetSettingWhenSettingNotExists() {
    $this->digtapMock->expects($this->once())->method('registerError');
    $this->assertEmpty($this->digtap->getSetting('non-existing setting', 'some error message'));
  }

  /**
   * Test getting a resource URL.
   */
  public function testGetResourceUrl() {
    $this->config['bestseller_url'] = self::CURRENT_BESTSELLER_API_URL;
    $this->mockDigtapService();
    $this->digtapMock->expects($this->never())->method('registerError');
    $this->assertNotEmpty($this->digtap->getResourceUrl('bestseller'));
  }

  /**
   * Test getting price categories from bestseller.
   */
  public function testGetPriceCategories() {
    $this->config['bestseller_url'] = self::CURRENT_BESTSELLER_API_URL;
    $this->mockDigtapService();
    $this->digtapMock->expects($this->never())->method('registerError');
    $this->assertNotEmpty($this->digtap->getPriceCategories());
  }
}
