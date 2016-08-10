<?php

namespace Drupal\Tests\hms_commerce\Unit;

/**
 * @coversDefaultClass \Drupal\hms_commerce\Encrypter
 * @group hms_commerce
 *
 * @todo Add tests
 */
class EncrypterTest extends DigtapTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  public function testExampleTest() {
    $this->mockEncrypterService();
    $this->assertTrue(TRUE);
    //todo
  }
}
