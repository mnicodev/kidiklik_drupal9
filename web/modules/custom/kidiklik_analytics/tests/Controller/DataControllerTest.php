<?php

namespace Drupal\kidiklik_analytics\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the kidiklik_analytics module.
 */
class DataControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "kidiklik_analytics DataController's controller functionality",
      'description' => 'Test Unit for module kidiklik_analytics and controller DataController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests kidiklik_analytics functionality.
   */
  public function testDataController() {
    // Check that the basic functions of module kidiklik_analytics.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
