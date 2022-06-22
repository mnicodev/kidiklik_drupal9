<?php

namespace Drupal\kidiklik_front\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the kidiklik_front module.
 */
class FavoriControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "kidiklik_front FavoriController's controller functionality",
      'description' => 'Test Unit for module kidiklik_front and controller FavoriController.',
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
   * Tests kidiklik_front functionality.
   */
  public function testFavoriController() {
    // Check that the basic functions of module kidiklik_front.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
