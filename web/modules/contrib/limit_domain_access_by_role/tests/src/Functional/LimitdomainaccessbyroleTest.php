<?php

namespace Drupal\Tests\limit_domain_access_by_role\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group limit_domain_access_by_role
 */
class LimitdomainaccessbyroleTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['limit_domain_access_by_role'];

  /**
   * A test user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  private $adminUser;

  /**
   * A user with "permission" to be blocked on non-technical domain.
   *
   * @var \Drupal\user\UserInterface
   */
  private $admintestUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // The user that manages the configuration only.
    $this->adminUser = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($this->adminUser);
    // Create an admin test user.
    $this->admintestUser = $this->drupalCreateUser(['administer site configuration', 'block access on non-technical domain'], 'Daffy Duck');
    // Create a non-admin test user.
    $this->user = $this->createUser([], 'Bobby Solo');
  }

  /**
   * Tests that the home page loads with a 200 response.
   */
  public function testLoad() {
    $this->drupalGet(Url::fromRoute('<front>'));
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Test form submission.
   */
  public function testFormValidation() {
    // Get the settingsform and check it loads correctly.
    $url = Url::fromRoute('limit_domain_access_by_role.settings');
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertText('Technical domains that are whitelisted to allow admin operations on');
    // Empty form submission. Check presence of the default value.
    $this->drupalPostForm(NULL, [], 'Save configuration');
    $this->assertText('*local*');
    // Form validation testing of an invalid characters rejection.
    $edit = [
      'technical_domains' => 'some/invalid/ch@r@ter$',
      'kill_sessions' => 1,
    ];
    $this->drupalPostForm(NULL, $edit, 'Save configuration');
    $this->assertText('Invalid characters are detected.');
    $this->assertText('Apart from yourself, there are currently no users with an open session (logged in)');
  }

  /**
   * Test the module's access control.
   */
  public function testAccessControl() {
    // Get the settingsform and check it loads correctly.
    $url = Url::fromRoute('limit_domain_access_by_role.settings');
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertText('Technical domains that are whitelisted to allow admin operations on');
    // A correct form submission.
    $edit = [
      'technical_domains' => \Drupal::request()->getHost(),
      'kill_sessions' => 1,
    ];
    $this->drupalPostForm(NULL, $edit, 'Save configuration');
    $this->assertText('The configuration options have been saved.');
    $this->assertText('There were no users with an open session to terminate.');
    // Log out.
    $this->drupalLogout();
    // Log in an unblocked user (non-admin) through the UI. Should access.
    $this->drupalGet('user');
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([
      'name' => $this->user->getAccountName(),
      'pass' => $this->user->passRaw,
    ], t('Log in'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertText('Bobby Solo');
    $this->assertText('Member for');
    // Log out.
    $this->drupalLogout();
    // Log in a blocked user (admin) through the UI. Should access.
    $this->drupalGet('user');
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([
      'name' => $this->admintestUser->getAccountName(),
      'pass' => $this->admintestUser->passRaw,
    ], t('Log in'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertText('Daffy Duck');
    $this->assertText('Member for');
    // Log out.
    $this->drupalLogout();
    // Get back to the settings form and check it loads correctly.
    $this->drupalLogin($this->adminUser);
    $url = Url::fromRoute('limit_domain_access_by_role.settings');
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertText('Technical domains that are whitelisted to allow admin operations on');
    // Make a correct form submission but now changing the technical domain.
    $edit = [
      'technical_domains' => 'admin.example.com',
      'kill_sessions' => 1,
    ];
    $this->drupalPostForm(NULL, $edit, 'Save configuration');
    $this->assertText('The configuration options have been saved.');
    $this->assertText('There were no users with an open session to terminate.');
    // Log out.
    $this->drupalLogout();
    // Log in an unblocked user (non-admin) through the UI. Should access.
    $this->drupalGet('user');
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([
      'name' => $this->user->getAccountName(),
      'pass' => $this->user->passRaw,
    ], t('Log in'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertText('Bobby Solo');
    $this->assertText('Member for');
    // Log out.
    $this->drupalLogout();
    // Log in a blocked user (admin) through the UI. Should get Access denied.
    $this->drupalGet('user');
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm([
      'name' => $this->admintestUser->getAccountName(),
      'pass' => $this->admintestUser->passRaw,
    ], t('Log in'));
    $this->assertSession()->statusCodeEquals(403);
    $this->assertText('Access denied');
    $this->assertText('You are not authorized to access this page.');
  }

}
