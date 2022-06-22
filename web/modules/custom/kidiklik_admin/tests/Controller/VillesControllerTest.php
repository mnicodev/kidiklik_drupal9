<?php

namespace Drupal\kidiklik_admin\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the kidiklik_admin module.
 */
class VillesControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "kidiklik_admin VillesController's controller functionality",
      'description' => 'Test Unit for module kidiklik_admin and controller VillesController.',
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
   * Tests kidiklik_admin functionality.
   */
  public function testVillesController() {
    // Check that the basic functions of module kidiklik_admin.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
