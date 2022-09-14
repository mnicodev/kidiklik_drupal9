<?php

namespace Drupal\kidiklik_front_newsletter\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Class DefaultForm.
 */
class DefaultForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'newsletter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['group'] = [
      '#type' => 'fieldset',
    ];
    $form['group']['nom'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nom'),
      '#size' => '40',
      '#weight' => '0',
      '#group' => 'group',
      '#attributes' => [

      ]
    ];
    $form['group']['prenom'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prénom'),
      '#weight' => '1',
      '#size' => '40',
      'attributes' => [

      ]
    ];
    $form['group']['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('E-mail'),
      '#required' => true,
      '#attributes' => [
        'placeholder' => 'xxxx@domain.aaa',
        'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
        'type' => 'email',

      ],
      '#default_value' => \Drupal::request()->get('email'),
      '#weight' => '1',
      '#size' => '40',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Envoyer'),
      '#weight' => '3',
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
    $database = \Drupal::database();

    $sql = "insert into inscrits_newsletters (email, nom, prenom, dept) values ('" . $form_state->getValue('email') . "','" . $form_state->getValue('nom') . "','" . $form_state->getValue('prenom') . "','" . get_departement() . "')";
    $query = $database->query($sql);

    $response = new RedirectResponse('newsletter.html?record_email=' . $form_state->getValue('email'));

    $form_state->setResponse($response);

  }

}
