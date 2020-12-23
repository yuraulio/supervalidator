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

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;
    // Attaching style and JS to the form.
    $form['#attached'] = ['library' => ['supervalidator/form']];

    // Gather the current form structure state.
    $tables_state = $form_state->getValues();
    $years_list = array_keys($tables_state['tables'][1]['table']);
    // We have to ensure that there is at least one name field.
    if ($tables_state === NULL) {
      $tables_state = 1;
    }
    array_unshift($years_list, '2017');
    $form['tables'] = $this->buildTable(1, $years_list);

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function addOne(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['tables'];
  }

  protected function buildYear($year_value, &$header) {
    foreach ($header as $title) {
      $year[$year_value][$title]['#attributes'] = [
        'class' => [
          'table-cell-data',
        ],
      ];
      switch ($title) {
        case 'Year':
          $year[$year_value][$title] = [
            '#plain_text' => $year_value,
          ];
          break;

        default:
          $year[$year_value][$title] = [
            '#type' => 'textfield',
            '#size' => 5,
          ];
      }
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

  protected function buildTable($table_num, $years_list) {
    $table[$table_num] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Table #@table_number', ['@table_number' => $table_num]),
      '#prefix' => '<div id="table-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    $table[$table_num]['actions'] = [
      '#type' => 'actions',
      '#weight' => -100,
    ];

    $table[$table_num]['actions']['add_year'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add year'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'table-fieldset-wrapper',
      ],
    ];

    $table[$table_num]['table'] = [
      '#type' => 'table',
//      '#caption' => $this
//        ->t('Table #@table_number', ['@table_number' => $table_num]),
      '#header' => $this->buildHeader(),
      '#sticky' => TRUE,
      '#header_columns' => 18,
    ];

    foreach ($years_list as $year_value) {
      $table[$table_num]['table'] += $this
        ->buildYear($year_value, $table[$table_num]['table']['#header']);
    }
    return $table;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
//    if ($form_state->getValue('value') != 'OK') {
//      $form_state->setErrorByName('value', $this->t('Invalid.'));
//    }
    $v = $form_state->getValues();
    $v = 0;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Valid');
  }

}
