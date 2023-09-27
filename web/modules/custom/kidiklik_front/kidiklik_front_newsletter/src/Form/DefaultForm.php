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
    $form['group']['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => $this->t('<p><b>Découvrez chaque semaine les meilleurs bons plans de sorties et activités à faire en famille avec Kidiklik !</b><br>
      Inscrivez-vous à notre newsletter pour recevoir directement dans votre boîte mail toutes les informations sur les événements et<br>
      activités pour enfants près de chez vous. <span >Ne manquez plus aucune occasion de partager des moments magiques en famille !</span></p>'),
      '#prefix' => '<div class="col-sm-12 col-md-12 intro">',
      '#suffix' => '</div>'
    ];
    $form['group']['nom'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nom'),
      '#size' => '40',
      '#weight' => '0',
      '#group' => 'group',
      '#attributes' => [
        'class' => [
          'form-control'
        ]
      ],
      '#prefix' => '<div class="col-sm-12 col-md-6">',
      '#suffix' => '</div>'
    ];
    $form['group']['prenom'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prénom'),
      '#weight' => '1',
      '#size' => '40',
      '#attributes' => [
        'class' => [
          'form-control'
        ]
      ],
      '#prefix' => '<div class="col-sm-12 col-md-6">',
      '#suffix' => '</div>'
    ];
    $form['group']['email'] = [
      '#type' => 'textfield',
      '#title' => 'E-mail',
      '#required' => true,
      '#attributes' => [
        'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
        'type' => 'email',
        'class' => [
          'form-control'
        ]
      ],
      '#default_value' => \Drupal::request()->get('email'),
      '#weight' => '1',
      '#size' => '40',
      '#prefix' => '<div class="col-sm-12 col-md-6">',
      '#suffix' => '</div>'
    ];
    $form['group']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t("S'inscrire à la newsletter"),
      '#weight' => '3',
      '#prefix' => '<div class="col-sm-12 col-md-6">',
      '#suffix' => '</div>'
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
