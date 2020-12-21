<?php

namespace Drupal\supervalidator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements a form with AJAX validation for adding feedback.
 */
class SuperForm extends FormBase {

  /**
   * Just return the form ID.
   */
  public function getFormId() {
    return 'super_form';
  }


  public function buildForm(array $form, FormStateInterface $form_state, $post = FALSE) {
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#name' => 'validate',
      '#button_type' => 'primary',
      '#value' => $this->t('Validate it!'),
    ];

    // Attaching style to the form.
    $form[] = ['#attached' => ['library' => ['supervalidator/form']]];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('value') != 'OK') {
      $form_state->setErrorByName('value', $this->t('Invalid.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Valid');
  }

}
