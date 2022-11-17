<?php

namespace Drupal\kidiklik_front_newsletter\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the kidiklik_front_newsletter module.
 */
class DefaultControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "kidiklik_front_newsletter DefaultController's controller functionality",
      'description' => 'Test Unit for module kidiklik_front_newsletter and controller DefaultController.',
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
   * Tests kidiklik_front_newsletter functionality.
   */
  public function testDefaultController() {
    // Check that the basic functions of module kidiklik_front_newsletter.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
