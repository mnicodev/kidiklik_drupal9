<?php

namespace Drupal\limit_domain_access_by_role\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LimitDomainAccessByRoleConfigForm.
 */
class LimitDomainAccessByRoleConfigForm extends ConfigFormBase {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * Drupal\Core\Session\SessionManagerInterface definition.
   *
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  protected $sessionManager;

  /**
   * The string translation manager.
   *
   * @var \Drupal\Core\StringTranslation\TranslationManager
   */
  protected $translationManager;

  /**
   * Use core services object.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    Connection $database,
    DateFormatterInterface $date_formatter,
    EntityTypeManagerInterface $entity_type_manager,
    Messenger $messenger,
    SessionManagerInterface $session_manager,
    TranslationManager $translation_manager
  ) {
    $this->configFactory = $config_factory;
    $this->database = $database;
    $this->dateFormatter = $date_formatter;
    $this->entityTypeManager = $entity_type_manager;
    $this->messenger = $messenger;
    $this->sessionManager = $session_manager;
    $this->translationManager = $translation_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('database'),
      $container->get('date.formatter'),
      $container->get('entity_type.manager'),
      $container->get('messenger'),
      $container->get('session_manager'),
      $container->get('string_translation')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'limit_domain_access_by_role.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'limit_domain_access_by_role_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('limit_domain_access_by_role.settings');
    $permission = 'block access on non-technical domain';
    $total = (int) $this->countLoggedInUsers() - 1;
    $session_lifetime = $this->dateFormatter->formatInterval(ini_get("session.gc_maxlifetime"));
    $number = (int) count($this->getUsersWithPermission($permission)) - 1;
    if ($number < 1) {
      $this->messenger()->addStatus($this->t("Apart from yourself, there are currently no users with an open session (logged in) that have '%permission' applied to them (admin users).", ['%permission' => $permission]));
    }
    else {
      $this->messenger()->addWarning($this->translationManager->formatPlural($total, "Apart from yourself, there is currently @number out of 1 user with an open user session (logged in) that has '%permission' applied (admin user).", "Apart from yourself, there are currently @number out of @count users with an open user session (logged in) that have '%permission' applied to them (admin users).", [
        '@number' => $number,
        '%permission' => $permission,
      ]));
    }
    if ($config->hasOverrides('technical_domains')) {
      $this->messenger->addMessage($this->t("The whitelisted technical domains are defined in the settings.php file using <a href='https://www.drupal.org/docs/8/api/configuration-api/configuration-override-system'>Drupal's configuration override system</a>. The corresponding field can therefore not be changed here."), 'warning');
    }

    $form['form_description'] = [
      '#markup' => $this->t("These settings do only affect users that have @block_access applied to them (admin users). Once they log in, they will only succeed to do so when they are on the whitelisted technical domain (with extra protection). They still use the same database. @more_info.", [
        '%permission' => $permission,
        '@block_access' => Link::fromTextAndUrl($permission, Url::fromRoute('user.admin_permissions', [], [
          'fragment' => 'module-limit_domain_access_by_role',
          'attributes' => [
            'title' => $this->t('Permissions'),
          ],
        ]))->toString(),
        '@more_info' => Link::fromTextAndUrl($this->t('More info'), Url::fromUri('https://www.drupal.org/project/limit_domain_access_by_role/', [
          'attributes' => [
            'title' => $this->t('Project page on Drupal.org'),
            'target' => '_blank',
          ],
        ]))->toString(),
      ]),
    ];
    $form['technical_domains'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Technical domains that are whitelisted to allow admin operations on'),
      '#description' => $this->t('Provide the domain hosts to be used as technical domain to allow (admin) roles to access. One domain per line. Asterisk wildcards are supported, like *.example.com.'),
      '#default_value' => str_replace(',', PHP_EOL, $config->get('technical_domains')),
      '#disabled' => $config->hasOverrides('technical_domains'),
    ];
    $form['kill_sessions'] = [
      '#title' => $this->t("Terminate all open sessions on form save of users, apart from yourself, that have '%permission' applied to them (admin users). It will not affect others.", ['%permission' => $permission]),
      '#description' => $this->t('TAKE CARE: Users might lose their current edits.<br />Leaving this unchecked implies that users with an open session (not having logged out) are allowed to finish their current session. Those sessions will "live" until they are inactive for the duration of the session lifetime or logout. On this site, the session timeout is currently set to %lifetime seconds (approx. %session_lifetime). Be aware that the session timestamp is when a session last requested a page. That means that if a user never logs out and keeps on visiting the site within the duration of the session lifetime, they can keep their session alive indefinite. That is why you have the option to "throw out" these users and force them to log in again. This can be done at any time by saving this form, even unchanged.', ['%lifetime' => ini_get("session.gc_maxlifetime"), '%session_lifetime' => $session_lifetime]),
      '#type' => 'checkbox',
      '#default_value' => FALSE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Check just for invalid characters using urlencode().
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (str_replace([
      PHP_EOL,
      "\r\n",
      "\r",
      "\n",
      "*",
    ], ".", $form_state->getValue('technical_domains')) !== urlencode(str_replace([
      PHP_EOL,
      "\r\n",
      "\r",
      "\n",
      "*",
    ], ".", $form_state->getValue('technical_domains')))) {
      $config = $this->configFactory->get('limit_domain_access_by_role.settings');
      $overrides = $config->hasOverrides('technical_domains') ? ' ' . $this->t('Check the defined technical domains in the settings.php file.') : NULL;
      $form_state->setErrorByName('technical_domains', $this->t('Invalid characters are detected.@overrides', ['@overrides' => $overrides]));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $current_user_id = $this->currentUser()->id();
    if ($form_state->getValue('kill_sessions')) {
      $permission = 'block access on non-technical domain';
      $users = $this->getUsersWithPermission($permission);
      foreach ($users as $uid) {
        // Prevent destroying the user session of user UID=1 and anonymous.
        if ($uid <= 1) {
          continue;
        }
        // Prevent the current user from destroying their own session.
        if ($uid != $current_user_id) {
          $this->sessionManager->delete($uid);
        }
      }
      if ((int) count($users) - 1 > 0) {
        $this->messenger()->addStatus($this->translationManager->formatPlural((int) count($users) - 1, 'The session of 1 user has been terminated successfully.', 'The sessions of @count users have been terminated successfully.'));
      }
      else {
        $this->messenger()->addStatus($this->t("There were no users with an open session to terminate.", ['%permission' => $permission]));
      }
    }
    $this->config('limit_domain_access_by_role.settings')
      ->set('technical_domains', str_replace(["\r\n", "\r", "\n"], ",", $form_state->getValue('technical_domains')))
      ->save();

  }

  /**
   * Count all current logged in users.
   *
   * @return int
   *   A total of logged in users.
   */
  public function countLoggedInUsers() {
    $query = $this->database->select('sessions', 'a');
    $query->fields('a', ['uid']);
    $count = (int) $query->countQuery()->execute()->fetchField();
    return $count;
  }

  /**
   * Get all users that have a specific permission.
   *
   * @param string $permission
   *   String of permission.
   *
   * @return array
   *   An array of user ids.
   */
  public function getUsersWithPermission($permission) {
    $ids = [];
    $roles = $this->entityTypeManager->getStorage('user_role')->loadMultiple();
    foreach ($roles as $role) {
      $has_permission = $role->hasPermission($permission);
      if ($role->id() != 'anonymous' && $role->id() != 'authenticated' && $has_permission) {
        $query = $this->database->select('sessions', 'a');
        $query->fields('a', ['uid']);
        $query->join('user__roles', 'b', 'a.uid = b.entity_id AND b.roles_target_id = :role', [':role' => $role->id()]);
        $ids = array_merge($ids, $query->execute()->fetchCol());
      }
    }
    return array_unique($ids);
  }

}
