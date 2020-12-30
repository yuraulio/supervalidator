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

  protected function buildHeader() {
    // Add the year column title.
    $header[0] = 'Year';

    // Fill the header array with the month names and
    // names of the quarters of the year.
    for ($i = 1; $i < 13; $i++) {
      $timestamp = mktime(0, 0, 0, $i, 1);
      $header[$i] = date('M', $timestamp);
      // After each of 3 month add the appropriate quarter title.
      if ($i % 3 == 0) {
        $header['Q' . intdiv($i, 3)] = 'Q' . intdiv($i, 3);
      }
    }

    // Add the year summary column title.
    $header[] = 'YTD';

    return $header;
  }

  protected function buildYear($year_value, &$header) {
    $year = [];
    foreach ($header as $key => $title) {
      $year[$year_value][$title] = [
        '#type' => 'number',
        '#min' => 0,
        '#step' => 0.01,
      ];
      switch ($title) {
        case 'Year':
          $year[$year_value][$title] = [
            '#plain_text' => $year_value,
          ];
          break;

        case 'Q1':
        case 'Q2':
        case 'Q3':
        case 'Q4':
          $year[$year_value][$title]['#attributes'] = [
            'class' => ['quarter'],
          ];
          break;

        case 'YTD':
          $year[$year_value][$title]['#attributes'] = [
            'class' => ['year'],
          ];
          break;

        default:
          $year[$year_value][$key] = $year[$year_value][$title];
          unset($year[$year_value][$title]);
      }
    }

    return $year;
  }

  protected function buildTable($table_num, $years_list) {
    $table = [
      '#type' => 'fieldset',
      '#title' => $this->t('Table #@table_number', ['@table_number' => $table_num]),
      '#prefix' => '<div id="table-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    $table['actions'] = [
      '#type' => 'actions',
      '#weight' => -100,
    ];

    $table['actions']['add_year'] = [
      '#type' => 'button',
      '#name' => 'addYear_' . $table_num,
      '#value' => $this->t('Add year'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'super_form',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Adding a year...',
        ],
        'effect' => 'slide',
        'speed' => 800,
      ],
    ];

    $table['rows'] = [
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#sticky' => TRUE,
      '#header_columns' => 18,
    ];

    foreach ($years_list as $year_value) {
      $table['rows'] += $this
        ->buildYear($year_value, $table['rows']['#header']);
      $table['rows'][$year_value]['#attributes'] = [
        'id' => "$table_num-$year_value",
      ];
    }
    return $table;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;
    // Attaching style and JS to the form.
    $form['#attached'] = ['library' => ['supervalidator/form']];
    $form['#attributes'] = [
      'id' => $this->getFormId(),
    ];

    // Gather the current form structure state.
    $tables_state = $form_state->getValues()['tables'] ?? NULL;
    $button = $form_state->getTriggeringElement()['#name'] ?? '';
    $table_to_add = FALSE;

    // We have to ensure that there is at least one name field.
    if ($tables_state === NULL) {
      $tables_count = 1;
    }
    else {
      $tables_count = count($tables_state);
    }
    $button = preg_split("/_/s", $button);
    switch ($button[0]) {
      case 'addTable':
        $tables_count++;
        break;

      case 'addYear':
        $table_to_add = $button[1];
    }
    for ($i = 1; $i <= $tables_count; $i++) {
      $years_list = isset($tables_state[$i]) ? array_keys($tables_state[$i]['rows']) : [date('Y')];

      if ($table_to_add == $i) {
        $min = min($years_list);
        array_unshift($years_list, $min - 1);
      }
      $form['tables'][$i] = $this->buildTable($i, $years_list);
    }

    $form['actions']['add_table'] = [
      '#type' => 'button',
      '#name' => 'addTable',
      '#value' => $this->t('Add table'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'super_form',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Adding a table...',
        ],
        'effect' => 'slide',
        'speed' => 800,
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function addOne(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $tables = $form_state->getValue('tables');
    $period = [];

    foreach ($tables as $table_num => $table) { // Tables.
      $start = NULL;
      $end = NULL;
      $completed = FALSE;
      foreach ($table['rows'] as $year => $months) { // Years.
        for ($i = 1; $i <= 12; $i++) {  // Months.
          if ($months[$i] !== '') {
            if ($completed) {
              $form_state->setError($form['tables'][$table_num]['rows'][$year][$i], 'Invalid!');
              break(3);
            }
            else {
              if (!$start) {
                $start = mktime(0, 0, 0, $i, 1, $year);
                $end = $start;
              }
              else {
                $end = mktime(0, 0, 0, $i, 1, $year);
              }
            }
          }
          else {
            if ($end) {
              $completed = TRUE;
            }
          }
        }
      }
      if ($period) {
        if (($period['start'] !== $start) || ($period['end'] !== $end)) {
          $form_state->setError($form['tables'][$table_num], 'Invalid!');
          break;
        }
      }
      else {
        $period['start'] = $start;
        $period['end'] = $end;
      }
      if ($start && $end) {
        $this->messenger()->addMessage(date('d-M-Y', $start));
        $this->messenger()->addMessage(date('d-M-Y', $end));
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Valid');
  }

}
