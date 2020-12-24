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
    $what_to_do = substr($button, 0, -1);
    switch ($what_to_do) {
      case 'addTable':
        $tables_count++;
        break;

      case 'addYear':
        $table_to_add = substr($button, -1);
    }
    for ($i = 1; $i <= $tables_count; $i++) {
      $years_list = isset($tables_state[$i]) ? array_keys($tables_state[$i]['rows']) : [date('Y')];
//      if ($tables_state) {
//        $years_list = array_keys($tables_state[$i]['rows']);
//      }
//      else {
//        $years_list = [date('Y')];
//      }
      if ($table_to_add == $i) {
        $min = min($years_list);
        array_unshift($years_list, $min - 1);
      }
      $form['tables'][$i] = $this->buildTable($i, $years_list);
    }

    $form['actions']['add_table'] = [
      '#type' => 'button',
      '#name' => 'addTable1',
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

  protected function buildYear($year_value, &$header) {
    foreach ($header as $title) {
      switch ($title) {
        case 'Year':
          $year[$year_value][$title] = [
            '#plain_text' => $year_value,
          ];
          break;

        default:
          $year[$year_value][$title] = [
            '#type' => 'textfield',
            '#size' => 4,
          ];
      }
      $year[$year_value][$title]['#attributes'] = [ // TODO: Check if necessary.
        'class' => [
          strtolower($title),
        ],
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
      '#name' => 'addYear' . $table_num,
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
//      '#caption' => $this
//        ->t('Table #@table_number', ['@table_number' => $table_num]),
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

  public function validateForm(array &$form, FormStateInterface $form_state) {
//    if ($form_state->getValue('value') != 'OK') {
//      $form_state->setErrorByName('value', $this->t('Invalid.'));
//    }
    $v = $form_state->getTriggeringElement()['#name'];
    $v = 0;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Valid');
  }

}
