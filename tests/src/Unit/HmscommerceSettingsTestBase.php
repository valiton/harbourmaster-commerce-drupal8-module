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

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Create a dummy container.
    $this->container = new ContainerBuilder();

    // Initial module set up.
    $this->config = [
      'bestseller_url' => '',
      'entitlement_group_name' => 'hasAbo'
    ];
    $this->mockDigtapService();
  }

  /**
   * Mock Drupal Digtap service.
   */
  protected function mockDigtapService() {
    $this->configFactory = $this->getConfigFactoryStub(['hms_commerce.settings' => $this->config]);
    $this->digtapMock = $this->getMockBuilder('\Drupal\hms_commerce\Digtap')
      ->setConstructorArgs([$this->configFactory])
      ->getMock();
    $this->container->set('hms_commerce.settings', $this->digtapMock);
    \Drupal::setContainer($this->container);
    $this->digtap = \Drupal::service('hms_commerce.settings');
  }
}
