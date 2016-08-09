<?php

namespace Drupal\Tests\hms_commerce\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\DependencyInjection\ContainerBuilder;

/**
 * Tests hms_commerce settings.
 *
 * @group hms_commerce
 */
class HmscommerceSettingsTestBase extends UnitTestCase {

  protected $config;
  protected $digtap;
  protected $digtapMock;
  private $container;
  private $configFactory;
  protected $backupGlobals = FALSE;

  /**
   * Used to set a Drupal global. Does not need to be a real URL ATM.
   */
  const BASE_URL = 'https://some-url';

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Create a dummy container.
    $this->container = new ContainerBuilder();

    // The string translation service will be used for most test cases.
    $this->container->set('string_translation', $this->getStringTranslationStub());

    // Initial config set up. These are the settings the module sets upon
    // installation (see hms_commerce.settings.yml).
    $this->config = [
      'bestseller_url' => '',
      'entitlement_group_name' => 'hasAbo',
    ];

    // Mock the digtap service with the above settings.
    $this->mockDigtapService();

    // Set this Drupal global as it is used in tested class.
    $GLOBALS['base_url'] = self::BASE_URL;
  }

  /**
   * Mock Drupal Digtap service.
   */
  protected function mockDigtapService() {
    $this->configFactory = $this->getConfigFactoryStub(['hms_commerce.settings' => $this->config]);
    $this->digtapMock = $this->getMockBuilder('\Drupal\hms_commerce\Digtap')
      ->setConstructorArgs([$this->configFactory])
      ->setMethods(['registerError'])
      ->getMock();
    $this->refreshDigtapService();
  }

  /**
   * Adds the Digtap class to the Drupal service container so it can be used
   * as a Drupal service.
   */
  protected function refreshDigtapService() {
    $this->container->set('hms_commerce.settings', $this->digtapMock);
    \Drupal::setContainer($this->container);
    $this->digtap = \Drupal::service('hms_commerce.settings');
  }
}
