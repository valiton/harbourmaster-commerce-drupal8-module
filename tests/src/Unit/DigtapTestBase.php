<?php

namespace Drupal\Tests\hms_commerce\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\DependencyInjection\ContainerBuilder;

/**
 * Tests hms_commerce settings.
 */
abstract class DigtapTestBase extends UnitTestCase {

  protected $config;
  protected $container;
  protected $backupGlobals = FALSE;

  protected $digtapMock;
  protected $premiumContentManagerMock;
  protected $encrypterMock;

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
    $configFactory = $this->getConfigFactoryStub(['hms_commerce.settings' => $this->config]);

    $logger = $this->getMockBuilder('\Drupal\hms_commerce\Logger')
      ->disableOriginalConstructor()
      ->getMock();

    $current_user = $this->getMockBuilder('\Drupal\Core\Session\AccountProxyInterface')
      ->disableOriginalConstructor()
      ->getMock();

    $this->digtapMock = $this->getMockBuilder('\Drupal\hms_commerce\Digtap')
      ->setConstructorArgs([$configFactory, $logger, $current_user])
      ->getMock();
    $this->container->set('hms_commerce.settings', $this->digtapMock);
    \Drupal::setContainer($this->container);
  }

  /**
   * Mock Drupal PremiumContentManager service.
   */
  protected function mockPremiumContentManagerService() {
    $this->mockDigtapService();
    $this->mockEncrypterService();
    $this->premiumContentManagerMock = $this->getMockBuilder('\Drupal\hms_commerce\PremiumContentManager')
      ->setConstructorArgs([\Drupal::service('hms_commerce.settings'), \Drupal::service('hms_commerce.encrypter')])
      ->setMethods(NULL)
      ->getMock();
    $this->container->set('hms_commerce.premium_content_manager', $this->premiumContentManagerMock);
    \Drupal::setContainer($this->container);
  }

  /**
   * Mock Drupal PremiumContentManager service.
   */
  protected function mockEncrypterService() {
    $this->encrypterMock = $this->getMockBuilder('\Drupal\hms_commerce\Encrypter')
      ->getMock();
    $this->container->set('hms_commerce.encrypter', $this->encrypterMock);
    \Drupal::setContainer($this->container);
  }
}
