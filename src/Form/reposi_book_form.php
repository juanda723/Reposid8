<?php

namespace Drupal\reposi\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an example form.
 */
class reposi_book_form extends FormBase {

public function getFormId() {
    return 'reposi_form';
  }


public function buildForm(array $form, FormStateInterface $form_state) {

  global $_reposi_start_form;
  $markup = '<p>' . '<i>' . t('You must complete the required fields before the
            add authors.') . '</i>' . '</p>';
  $form['body'] = array('#markup' => $markup);
  $form['title'] = array(
    '#title' => t('Title'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#maxlength' => 511,
  );
  $form['sub'] = array(
    '#title' => t('Subtitle'),
    '#type' => 'textfield',
  );
  $form['description'] = array(
    '#title' => t('Description'),
    '#type' => 'textarea',
  );
  $form_state['storage']['author'] = isset($form_state['storage']['author'])?
                                     $form_state['storage']['author']:1;
  $form['author_wrapper'] = array(
    '#type' => 'fieldset',
    '#title' => t('Authors'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['author_wrapper']['author'] = array(
    '#type' => 'container',
    '#tree' => TRUE,
    '#prefix' => '<div id="author">',
    '#suffix' => '</div>',
  );
  $header = array (
    'first_name' => t('First name'),
    'second_name'=> t('Second name'),
    'f_lastname' => t('First last name'),
    's_lastname' => t('Second last name'),
  );
  $options = array();
  for ($i = 1; $i <= $form_state['storage']['author']; $i++) {
    $options[$i] =array(
      'first_name' => array(
        'data' => array(
          'first_name_' . $i => array(
            '#type' => 'textfield',
            '#value' => isset($form_state['input']['first_name_' . $i])?
                        $form_state['input']['first_name_' . $i]:'',
            '#name' => 'first_name_' . $i,
            '#size' => 16,
          )
        ),
      ),
      'second_name' => array(
        'data' => array(
          'second_name_' . $i => array(
            '#type' => 'textfield',
            '#value' => isset($form_state['input']['second_name_' . $i])?
                        $form_state['input']['second_name_' . $i]: '',
            '#name' => 'second_name_' . $i,
            '#size' => 16,
          )
        ),
      ),
      'f_lastname' => array(
        'data' => array(
          'f_lastname_' . $i => array(
            '#type' => 'textfield',
            '#value' => isset($form_state['input']['f_lastname_' . $i])?
                        $form_state['input']['f_lastname_' . $i]: '',
            '#name' => 'f_lastname_' . $i,
            '#size' => 16,
          )
        ),
      ),
      's_lastname' => array(
        'data' => array(
          's_lastname_' . $i => array(
            '#type' => 'textfield',
            '#value' => isset($form_state['input']['s_lastname_' . $i])?
                        $form_state['input']['s_lastname_' . $i]: '',
            '#name' => 's_lastname_' . $i,
            '#size' => 16,
          )
        ),
      ),
    );
  }
  $options = array_reverse($options);
  $aut = variable_get('aut',"");
  if ($i == 2) {
    variable_del('aut');
    $compare = $aut+2;
  } else {
    $aut = variable_get('aut');
    $compare = $aut+3;
  }
  variable_set('cont_aut',$aut);
  if ($compare == $i) {
    $form_state['storage']['author'] = $i;
  }
  $form['author_wrapper']['author']['table'] = array(
    '#type' => 'tableselect',
    '#header' => $header,
    '#options' => $options,
    '#empty' => t('No lines found'),
  );
  $form['author_wrapper']['add_author'] = array(
    '#type' => 'button',
    '#value' => t('Add author'),
    '#ajax' => array(
      'callback' => 'reposi_ajax_add_author',
      'wrapper' => 'author',
    ),
  );
  $options = array();
  for ($i = 1; $i <= $form_state['storage']['author']; $i++) {
    $options[$i] =array(
    'first_name' => array(
      'data' => array(
        'first_name_' . $i => array(
          '#type' => 'textfield',
          '#value' => isset($form_state['input']['first_name_' . $i])?
                      $form_state['input']['first_name_' . $i]:'',
          '#name' => 'first_name_' . $i,
          '#size' => 16,
        )
      ),
    ),
    'second_name' => array(
      'data' => array(
        'second_name_' . $i => array(
          '#type' => 'textfield',
          '#value' => isset($form_state['input']['second_name_' . $i])?
                      $form_state['input']['second_name_' . $i]: '',
          '#name' => 'second_name_' . $i,
          '#size' => 16,
        )
      ),
    ),
    'f_lastname' => array(
      'data' => array(
        'f_lastname_' . $i => array(
          '#type' => 'textfield',
          '#value' => isset($form_state['input']['f_lastname_' . $i])?
                      $form_state['input']['f_lastname_' . $i]: '',
          '#name' => 'f_lastname_' . $i,
          '#size' => 16,
        )
      ),
    ),
    's_lastname' => array(
      'data' => array(
        's_lastname_' . $i => array(
          '#type' => 'textfield',
          '#value' => isset($form_state['input']['s_lastname_' . $i])?
                      $form_state['input']['s_lastname_' . $i]: '',
          '#name' => 's_lastname_' . $i,
          '#size' => 16,
        )
      ),
    ),);
  }
  $options = array_reverse($options);
  $form['author_wrapper']['author']['table'] = array(
    '#type' => 'tableselect',
    '#header' => $header,
    '#options' => $options,
    '#empty' => t('No lines found'),
  );
  $form['year'] = array(
    '#title' => t('Publication year'),
    '#type' => 'textfield',
    '#size' => 5,
    '#required' => TRUE,
    '#description' => t('Four numbers'),
  );
  $form['langua'] = array(
    '#title' => t('Language'),
    '#type' => 'textfield',
  );
  $form['vol'] = array(
    '#title' => t('Volume/Series'),
    '#type' => 'textfield',
  );
  $form['issue'] = array(
    '#title' => t('Number (Issue)'),
    '#type' => 'textfield',
  );
  $form['edito'] = array(
    '#title' => t('Publisher'),
    '#type' => 'textfield',
  );
  $form['editor_name'] = array(
    '#title' => t('Publisher name'),
    '#type' => 'textfield',
    '#description' => t('Editor person'),
  );
  $form['pub'] = array(
    '#title' => t('Place of publication'),
    '#type' => 'textfield',
  );
  $form['issn'] = array(
    '#title' => t('ISSN'),
    '#type' => 'textfield',
  );
  $form['isbn'] = array(
    '#title' => t('ISBN'),
    '#type' => 'textfield',
  );
  $form['url'] = array(
    '#title' => t('URL'),
    '#type' => 'textfield',
    '#maxlength' => 511,
  );
  $form['doi'] = array(
    '#title' => t('DOI'),
    '#type' => 'textfield',
  );
  $form['save'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );
  $form['#validate'][] = 'reposi_book_title_validate';
  $form['#validate'][] = 'reposi_publiform_year2_validate';
  $form['#validate'][] = 'reposi_publiform_authorn_validate';
  $form['#validate'][] = 'reposi_publiform_authorl_validate';
  $_reposi_start_form=TRUE;
  return $form;

//////////////////////////////////////
public function reposi_book_title_validate(array &$form, FormStateInterface $form_state){
  $title_validate = $form_state['values']['title'];
  $search_book = db_select('reposi_article_book', 'ab');
  $search_book->fields('ab')
          ->condition('ab.ab_type', 'Book', '=')
          ->condition('ab.ab_title', $title_validate, '=');
  $info_book = $search_book->execute();
  $new_title=reposi_string($title_validate);
  foreach ($info_book as $titles) {
    $new_titles=reposi_string($titles->ab_title);
    if (strcasecmp($new_title, $new_titles) == 0) {
      form_set_error('name', t('This Book exists on Data Base.'));
    }
  }
}

public function reposi_publiform_year2_validate(array &$form, FormStateInterface $form_state){
  $year_validate = $form_state['values']['year'];
  if(!is_numeric($year_validate) || $year_validate > '9999' || $year_validate < '1000') {
    form_set_error('publi_year', t('It is not an allowable value for year.'));
  }
}

/////////////////////////////////////////////7autorn
////////////////////////////////////////autorl

public function reposi_publiform_authorn_validate(array &$form, FormStateInterface $form_state){
  $author2_validate = $form_state['input']['first_name_2'];
  if (empty($author2_validate)){
    form_set_error('first_name_2', t('One author is required as minimum
    (first name and last name).'));
  }
}

public function reposi_publiform_authorl_validate(array &$form, FormStateInterface $form_state){
  $author3_validate = $form_state['input']['f_lastname_2'];
  if (empty($author3_validate)){
    form_set_error('f_lastname_2', t('One author is required as minimum
    (first name and last name).'));
  }
}


public function reposi_book_form_submit(array &$form, FormStateInterface $form_state){
    $book_title = $form_state['input']['title'];
    $book_sub = $form_state['input']['sub'];
    $book_des = $form_state['input']['description'];
    $book_year = $form_state['input']['year'];
    $book_langu = $form_state['input']['langua'];
    $book_vol = $form_state['input']['vol'];
    $book_issue = $form_state['input']['issue'];
    $book_edito = $form_state['input']['edito'];
    $book_edit_name = $form_state['input']['editor_name'];
    $book_pub = $form_state['input']['pub'];
    $book_issn = $form_state['input']['issn'];
    $book_isbn = $form_state['input']['isbn'];
    $book_url = $form_state['input']['url'];
    $book_doi = $form_state['input']['doi'];
    db_insert('reposi_article_book')->fields(array(
        'ab_type'              => 'Book',
        'ab_title'             => $book_title,
        'ab_subtitle_chapter'  => $book_sub,
        'ab_abstract'          => $book_des,
        'ab_language'          => $book_langu,
        'ab_journal_editorial' => $book_edito,
        'ab_publisher'         => $book_edit_name,
        'ab_place'             => $book_pub,
    ))->execute();
    $search_book = db_select('reposi_article_book', 'ab');
    $search_book->fields('ab')
            ->condition('ab.ab_type', 'Book', '=')
            ->condition('ab.ab_title', $book_title, '=');
    $book_id = $search_book->execute()->fetchField();
    $new_book_year = (int)$book_year;
    db_insert('reposi_date')->fields(array(
        'd_year' => $new_book_year,
        'd_abid' => $book_id,
    ))->execute();
    db_insert('reposi_publication')->fields(array(
        'p_type'  => 'Book',
        'p_title' => $book_title,
        'p_year'  => $new_book_year,
        'p_check' => 1,
        'p_abid'  => $book_id,
    ))->execute();
    if (!empty($book_vol) || !empty($book_issue) || !empty($book_issn) ||
        !empty($book_isbn) || !empty($book_url) || !empty($book_doi)) {
      db_insert('reposi_article_book_detail')->fields(array(
        'abd_volume'     => $book_vol,
        'abd_issue'      => $book_issue,
        'abd_issn'       => $book_issn,
        'abd_isbn'       => $book_isbn,
        'abd_url'        => $book_url,
        'abd_doi'        => $book_doi,
        'abd_abid'       => $book_id,
      ))->execute();
    }
    for ($a = 1; $a <= $form_state['storage']['author'] ; $a++) {
      if (!empty($form_state['input']['first_name_' . $a])) {
        $aut_fn[] = strtolower($form_state['input']['first_name_' . $a]);
      } else {
        $aut_fn[] = '';
      }
      if (!empty($form_state['input']['second_name_' . $a])) {
        $aut_sn[] = strtolower($form_state['input']['second_name_' . $a]);
      } else {
        $aut_sn[] = '';
      }
      if (!empty($form_state['input']['f_lastname_' . $a])) {
        $aut_fl[] = strtolower($form_state['input']['f_lastname_' . $a]);
      } else {
        $aut_fl[] = '';
      }
      if (!empty($form_state['input']['s_lastname_' . $a])) {
        $aut_sl[] = strtolower($form_state['input']['s_lastname_' . $a]);
      } else {
        $aut_sl[] = '';
      }
      $info_author = array('a_first_name'      => drupal_ucfirst($aut_fn[$a-1]),
                           'a_second_name'     => drupal_ucfirst($aut_sn[$a-1]),
                           'a_first_lastname'  => drupal_ucfirst($aut_fl[$a-1]),
                           'a_second_lastname' => drupal_ucfirst($aut_sl[$a-1]),
                          );
      if(!empty($form_state['input']['first_name_' . $a]) &&
         !empty($form_state['input']['f_lastname_' . $a])){
        $serch_a = db_select('reposi_author', 'a');
        $serch_a->fields('a')
                ->condition('a.a_first_name', $aut_fn[$a-1], '=')
                ->condition('a.a_second_name', $aut_sn[$a-1], '=')
                ->condition('a.a_first_lastname', $aut_fl[$a-1], '=')
                ->condition('a.a_second_lastname', $aut_sl[$a-1], '=');
        $serch_aut[$a-1] = $serch_a->execute()->fetchField();
        if (empty($serch_aut[$a-1])) {
          db_insert('reposi_author')->fields($info_author)->execute();
          $serch2_a = db_select('reposi_author', 'a');
          $serch2_a ->fields('a')
                    ->condition('a.a_first_name', $aut_fn[$a-1], '=')
                    ->condition('a.a_second_name', $aut_sn[$a-1], '=')
                    ->condition('a.a_first_lastname', $aut_fl[$a-1], '=')
                    ->condition('a.a_second_lastname', $aut_sl[$a-1], '=');
          $serch2_aut[$a-1] = $serch2_a->execute()->fetchField();
          $aut_publi_id = (int)$serch2_aut[$a-1];
          db_insert('reposi_publication_author')->fields(array(
            'ap_author_id' => $aut_publi_id,
            'ap_abid'      => $book_id,
          ))->execute();
        } else {
          $aut_publi_id2 = (int)$serch_aut[$a-1];
          db_insert('reposi_publication_author')->fields(array(
              'ap_author_id' => $aut_publi_id2,
              'ap_abid'      => $book_id,
          ))->execute();
        }
      } else {
        if(isset($form_state['input']['first_name_' . $a]) ||
           isset($form_state['input']['f_lastname_' . $a])){
          drupal_set_message(t('The authors without first name and first
          last name will not be save.'), 'warning');
        }
      }
    }
    drupal_set_message(t('Book: ') . $book_title . t(' was save.'));
    variable_del('aut');
  }

}

}
