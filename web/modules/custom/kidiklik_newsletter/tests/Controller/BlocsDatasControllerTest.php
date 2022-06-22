<?php

namespace Drupal\kidiklik_newsletter\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the kidiklik_newsletter module.
 */
class BlocsDatasControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "kidiklik_newsletter BlocsDatasController's controller functionality",
      'description' => 'Test Unit for module kidiklik_newsletter and controller BlocsDatasController.',
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
   * Tests kidiklik_newsletter functionality.
   */
  public function testBlocsDatasController() {
    // Check that the basic functions of module kidiklik_newsletter.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
