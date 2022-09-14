<?php

namespace Drupal\kidiklik_front\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;

/**
 * Provides a 'Block kidiklik condition' condition to enable a condition based in module selected status.
 *
 * @Condition(
 *   id = "block_condition",
 *   label = @Translation("Block kidiklik condition"),
 *   context = {
 *     "block" = @ContextDefinition("entity:block", required = FALSE , label = @Translation("block"))
 *   }
 * )
 *
 */
class BlockCondition extends ConditionPluginBase
{

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {

    $options = [
      '',
      'national' => 'National',
      'departemental' => 'Départemental'
    ];

    $form['type_site'] = [
      '#type' => 'select',
      '#title' => $this->t('Type de site'),
      '#default_value' => $this->configuration['type_site'],
      '#options' => $options,
      '#description' => $this->t('Condition d\'affichage'),
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    $this->configuration['type_site'] = $form_state->getValue('type_site');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return ['type_site' => ''] + parent::defaultConfiguration();
  }

  /**
   * Evaluates the condition and returns TRUE or FALSE accordingly.
   *
   * @return bool
   *   TRUE if the condition has been met, FALSE otherwise.
   */
  public function evaluate()
  {

    if ((get_departement() === 0 && $this->configuration['type_site'] === 'national') ||
      (get_departement() !== 0 && $this->configuration['type_site'] === 'departemental'))
      return true;
    if ((get_departement() === 0 && $this->configuration['type_site'] === 'departemental') ||
      (get_departement() !== 0 && $this->configuration['type_site'] === 'national'))
      return false;
    return true;
  }

  /**
   * Provides a human readable summary of the condition's configuration.
   */
  public function summary()
  {
    $module = $this->getContextValue('type_site');
    $modules = system_rebuild_module_data();

    $status = ($modules[$module]->status) ? t('enabled') : t('disabled');

    return t('The module @module is @status.', ['@module' => $module, '@status' => $status]);
  }

}
