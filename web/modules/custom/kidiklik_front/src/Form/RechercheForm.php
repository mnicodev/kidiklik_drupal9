<?php

namespace Drupal\kidiklik_front\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RechercheForm.
 */
class RechercheForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'recherche_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $database = \Drupal::database();
    $query = $database->query("select * from villes where code_postal like '" . get_departement() . "%'");
    $villes = $query->fetchAll();

    $options[''] = 'Filtrer par zone';
    $options['Géolocalisé'] = [
      'geo' => 'Autour de moi',
    ];
    foreach ($villes as $ville) {
      $options['Par ville'][$ville->commune] = $ville->commune;
    }

    $form['ville'] = [
      "#type" => "select",
      "#title" => "Où ?",
      "#options" => $options,
      "#weight" => -9,
      "#default_value" => \Drupal::Request()->get('ville')
    ];
    $options = [
      "" => "N'importe quand",
      "now" => "Aujourd'hui",
      "mercredi" => "Ce mercredi",
      "wd" => "Ce week-end",
      "semaine" => "Cette semaine",
      "date" => "Par date"
    ];
    $default = \Drupal::Request()->get('quand');
    $form['quand'] = [
      "#type" => "select",
      "#title" => "Quand ?",
      "#options" => $options,
      "#weight" => -7,
      "#default_value" => $default
    ];
    ksm($default);
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#method' => 'GET',
      '#attributes' => [
        'method' => 'GET'
      ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format' ? $value['value'] : $value));
    }
  }

}
