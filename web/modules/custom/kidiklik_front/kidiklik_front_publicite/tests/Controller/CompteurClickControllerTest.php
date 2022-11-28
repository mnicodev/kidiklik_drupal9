<?php

namespace Drupal\kidiklik_front_publicite\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the kidiklik_front_publicite module.
 */
class CompteurClickControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "kidiklik_front_publicite CompteurClickController's controller functionality",
      'description' => 'Test Unit for module kidiklik_front_publicite and controller CompteurClickController.',
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
   * Tests kidiklik_front_publicite functionality.
   */
  public function testCompteurClickController() {
    // Check that the basic functions of module kidiklik_front_publicite.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
