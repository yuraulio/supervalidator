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


  protected function buildYear($year_value, &$header) {
    foreach ($header as $item) {
      $year[$year_value][$item]['#attributes'] = [
        'class' => [
          'table-cell-data',
        ],
      ];
      $year[$year_value][$item]['input'] = [
        '#type' => 'textfield',
        '#size' => 5,
      ];
    }

    return $year;
  }

  protected function buildHeader() {
    // Add the year column title.
    $header[] = 'Year';

    // Fill the header array with the month names and
    // names of the quarters of the year.
    for ($i = 1; $i < 13; $i++) {
      $timestamp = mktime(0, 0, 0, $i, 1);
      $header[] = date('M', $timestamp);
      // After each of 3 month add the appropriate quarter title.
      if ($i % 3 == 0) {
        $header[] = 'Q' . intdiv($i, 3);
      }
    }

    // Add the year summary column title.
    $header[] = 'YTD';

    return $header;
  }

  protected function buildTable($table_num) {
    $table['table_fieldset'] = [
      '#type' => 'fieldset',
      '#prefix' => '<div id="table-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $table['table_fieldset'][$table_num] = [
      '#type' => 'table',
      '#caption' => $this
        ->t('Table #@table_number', ['@table_number' => $table_num]),
      '#header' => $this->buildHeader(),
      '#sticky' => TRUE,
    ];

    $table['table_fieldset'][$table_num] += $this->
      buildYear(2020, $table['table_fieldset'][$table_num]['#header']);
    return $table;
  }

  public function buildForm(array $form, FormStateInterface $form_state, $post = FALSE) {
    $form['#tree'] = TRUE;

//    $form['name'][1]['first'] = [
//      '#type' => 'textfield',
//      '#title' => $this->t('Value'),
//    ];
//
//    $form['name'][1]['last'] = [
//      '#type' => 'textfield',
//      '#title' => $this->t('Value'),
//    ];
//
//    $form['name'][2]['first'] = [
//      '#type' => 'textfield',
//      '#title' => $this->t('Value'),
//    ];
//
//    $form['name'][2]['last'] = [
//      '#type' => 'textfield',
//      '#title' => $this->t('Value'),
//    ];

//    $form['value'] = [
//      '#type' => 'textfield',
//      '#title' => $this->t('Value'),
//      '#required' => FALSE,
//    ];

//    $form['actions']['add'] = [
//      '#type' => 'button',
//      '#value' => $this
//        ->t('Add row'),
//    ];
//
//    $form['actions']['submit'] = [
//      '#type' => 'submit',
//      '#name' => 'validate',
//      '#button_type' => 'primary',
//      '#value' => $this->t('Validate it!'),
//    ];

    // Attaching style and JS to the form.
//    $form[] = ['#attached' => ['library' => ['supervalidator/form']]];
//
//    $form['contacts'] = array(
//      '#type' => 'table',
//      '#caption' => $this
//        ->t('Sample Table'),
//      '#header' => array(
//        $this
//          ->t('Name'),
//        $this
//          ->t('Phone'),
//      ),
//    );
//    for ($i = 1; $i <= 4; $i++) {
//      $form['contacts'][$i]['#attributes'] = array(
//        'class' => array(
//          'foo',
//          'baz',
//        ),
//      );
//      $form['contacts'][$i]['name'] = array(
//        '#type' => 'textfield',
//        '#title' => $this
//          ->t('Name'),
//        '#title_display' => 'invisible',
//      );
//      $form['contacts'][$i]['phone'] = array(
//        '#type' => 'tel',
//        '#title' => $this
//          ->t('Phone'),
//        '#title_display' => 'invisible',
//      );
//    }
//    $form['contacts'][]['colspan_example'] = array(
//      '#plain_text' => 'Colspan Example',
//      '#wrapper_attributes' => array(
//        'colspan' => 2,
//        'class' => array(
//          'foo',
//          'bar',
//        ),
//      ),
//    );
    $form[] = $this->buildTable(1);
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
//    if ($form_state->getValue('value') != 'OK') {
//      $form_state->setErrorByName('value', $this->t('Invalid.'));
//    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Valid');
  }

}
