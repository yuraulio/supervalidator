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
    // Gather the number of names in the form already.
    $num_names = $form_state->get('num_names');
    // We have to ensure that there is at least one name field.
    if ($num_names === NULL) {
      $name_field = $form_state->set('num_names', 1);
      $num_names = 1;
    }
    $form['#tree'] = TRUE;
    // Attaching style and JS to the form.
    $form[] = ['#attached' => ['library' => ['supervalidator/form']]];

    $form['tables'] = $this->buildTable(1);

    $form['actions']['add_table'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add table'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'table-fieldset-wrapper',
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
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
          $year[$year_value][$title]['cell'] = [
            '#plain_text' => $year_value,
          ];
          break;

        default:
          $year[$year_value][$title]['cell'] = [
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

  protected function buildTable($table_num) {
    $table['table'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Table #@table_number', ['@table_number' => $table_num]),
      '#prefix' => '<div id="table-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    $table['table']['actions'] = [
      '#type' => 'actions',
      '#weight' => -100,
    ];

    $table['table']['actions']['add_year'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add year'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'table-fieldset-wrapper',
      ],
    ];

    // If there is more than one name, add the remove button.
//    if ($num_names > 1) {
//      $form['names_fieldset']['actions']['remove_year'] = [
//        '#type' => 'submit',
//        '#value' => $this->t('Remove year'),
//        '#submit' => ['::removeCallback'],
//        '#ajax' => [
//          'callback' => '::addmoreCallback',
//          'wrapper' => 'table-fieldset-wrapper',
//        ],
//      ];
//    }

    $table['table'][$table_num] = [
      '#type' => 'table',
//      '#caption' => $this
//        ->t('Table #@table_number', ['@table_number' => $table_num]),
      '#header' => $this->buildHeader(),
      '#sticky' => TRUE,
    ];

    $table['table'][$table_num] += $this->
      buildYear(2020, $table['table'][$table_num]['#header']);
    return $table;
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
