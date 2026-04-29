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
      '#value' => $this->t('<p><b>Devenez un parent ultra-inspiré, une grand-mère dans le vent ou un enseignant époustouflant !</b><br>
      Inscrivez-vous à notre newsletter pour recevoir nos coups de cœur et tous les bons plans de la région directement dans votre boîte mail.</span></p>'),
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
      '#weight' => '2',
      '#size' => '40',
      '#prefix' => '<div class="col-sm-12 col-md-6">',
      '#suffix' => '</div>'
  ];

    $form['group']['newsletter'] = [
      '#weight' => '3',
        '#type' => 'checkboxes',
        '#title' => 'Choix de la newsletter',
        '#required' => true,
        '#attributes' => [
            'class' => [
            //    'form-control'
            ]
        ],
      '#prefix' => '<div class="col-sm-12 col-md-6">',
      '#suffix' => '</div>',
      '#options' => [
            'FAMILLE' => t('Famille (hebdo)'),
            'GP' => t('Grand-parent (avant chaque période de vacances)'),
            'PRO' => t("Professionnel de l'enfance (4 fois par an)"),
        ],
        '#option_attributes' => [
            'a' => ['class' => ['form-checkbox-inline']],
            'b' => ['class' => ['form-checkbox-inline']],
            'c' => ['class' => ['form-checkbox-inline']],
          ],
      ];

    $form['group']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t("S'inscrire à la newsletter"),
      '#weight' => '4',
      '#prefix' => '<div class="col-sm-12 col-md-6">',
      '#suffix' => '</div>'
    ];
    $form["#attached"]["library"][] = "kidiklik_front_newsletter/kidiklik_front_newsletter.styles";

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
      $token = insert_registration_newsletter($form_state->getValue('email'), $form_state->getValue('nom'), $form_state->getValue('prenom'), $form_state->getValue('newsletter'));

    $response = new RedirectResponse('registration.html?token=' . $token);
    $response->send();
    exit;
  }

}
