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


  protected function buildYear($year_value) {
    $year[$year_value][$year_value] = 1;
  }

  protected function buildHeader() {
    // Generate array with the months numbers and names.
    $months = [];
    for ($i = 0; $i < 12; $i++) {
      $timestamp = mktime(0, 0, 0, $i + 1, 1);
      $months[date('n', $timestamp)] = date('M', $timestamp);
    }
    // Add the year column title.
    $header[] = 'Year';
    // Fill the header array with the month names and
    // names of the quarters of the year.
    foreach ($months as $month_num => $month_name) {
      $header[] = $month_name;
      // After each of 3 month add the appropriate quarter title.
      if ($month_num % 3 == 0) {
        $header[] = 'Q' . intdiv($month_num, 3);
      }
    }
    // Add the year summary column title.
    $header[] = 'YTD';

    return $header;
  }

  protected function buildTable($table_num) {
    $table = [
      '#type' => 'table',
      '#caption' => $this
        ->t('Table #@table_number', ['@table_number' => $table_num]),
      '#header' => $this->buildHeader(),
      '#sticky' => TRUE,
    ];
    foreach ($table['#header'] as $title) {
      $table[$table_num]['2020'][$title]['#attributes'] = [
        'class' => [
          'table-cell-data',
        ],
      ];
      $table['2020'][$title]['input'] = [
        '#type' => 'textfield',
        '#size' => 5,
      ];
    }

    return $table;
//    $table = [];
//    $table += buildYear($year_value);
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
