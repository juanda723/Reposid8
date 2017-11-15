<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\reposi\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;


class reposi_info_article_free extends Formbase {
  
public function getFormId() {
    return 'reposi_info_article_free';
  }
public function buildForm(array $form, FormStateInterface $form_state) {

$art_id = \Drupal::routeMatch()->getParameter('node');
$form['pid'] = array(
    '#type' => 'value',
    '#value' => $art_id,
  );
return $form;
}

public function validateForm(array &$form, FormStateInterface $form_state) 
  {
  
}
  public function submitForm(array &$form, FormStateInterface $form_state) {
$art_id = \Drupal::routeMatch()->getParameter('node');
global $base_url;
  
  $search_art = db_select('reposi_article_book', 'ab');
  $search_art->fields('ab')
          ->condition('ab.abid', $art_id, '=');
  $info_publi = $search_art->execute()->fetchAssoc();
  $search_art_detail = db_select('reposi_article_book_detail', 'abd');
  $search_art_detail->fields('abd')
          ->condition('abd.abd_abid', $art_id, '=');
  $info_publi_2 = $search_art_detail->execute()->fetchAssoc();
  $search_date = db_select('reposi_date', 'd');
  $search_date->fields('d')
              ->condition('d.d_abid', $art_id, '=');
  $art_date = $search_date->execute()->fetchAssoc();
  $format_date = reposi_formt_date($art_date['d_day'],$art_date['d_month'],$art_date['d_year']);
  $search_p_a_art = db_select('reposi_publication_author', 'pa');
  $search_p_a_art->fields('pa')
                ->condition('pa.ap_abid', $art_id, '=');
  $couple_art = $search_p_a_art->execute();
  $authors_art = '';
  $num_aut = $couple_art->rowCount();
  $flag_aut = 0;
  foreach ($couple_art as $aut_art) {
    $flag_aut++;
    $search_aut = db_select('reposi_author', 'a');
    $search_aut->fields('a')
               ->condition('a.aid', $aut_art->ap_author_id, '=');
    $each_aut = $search_aut->execute()->fetchAssoc();
    if ($flag_aut <> $num_aut) {
      $f_name = reposi_string($each_aut['a_first_name']);
      if (!empty($each_aut['a_second_name'])) {
        $s_name = reposi_string($each_aut['a_second_name']);
        $authors_art = $authors_art . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '. ' . $s_name[0] . '.',
                      $base_url . '/reposi/author/' . $aut_art->ap_author_id) . '.';
      } else {
        $authors_art = $authors_art . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '.', $base_url . '/reposi/author/' . $aut_art->ap_author_id) . '.';
      }
    } else {
      $search_aut = db_select('reposi_author', 'a');
      $search_aut->fields('a')
                 ->condition('a.aid', $aut_art->ap_author_id, '=');
      $each_aut = $search_aut->execute()->fetchAssoc();
      $f_name = reposi_string($each_aut['a_first_name']);
      if (!empty($each_aut['a_second_name'])) {
        $s_name = reposi_string($each_aut['a_second_name']);
        $authors_art = $authors_art . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '. ' . $s_name[0] . '.',
                      $base_url . '/reposi/author/' . $aut_art->ap_author_id) . '.';
      } else {
        $authors_art = $authors_art . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] . 
                      ' ' . $f_name[0] . '.', $base_url . '/reposi/author/' . $aut_art->ap_author_id) . '.';
      }
    }
  }
  $search_p_k = db_select('reposi_publication_keyword', 'pk');
  $search_p_k->fields('pk')
            ->condition('pk.pk_abid', $art_id, '=');
  $keyword = $search_p_k->execute();
  $flag_keyw = 0;
  $keyws_art = '';
  foreach ($keyword as $key_art) {
    $flag_keyw++;
    $search_keyw = db_select('reposi_keyword', 'k');
    $search_keyw->fields('k')
                ->condition('k.kid', $key_art->pk_keyword_id, '=');
    $keywords = $search_keyw->execute()->fetchAssoc();
    if ($flag_keyw == 1) {
      $keyws_art = l($keywords['k_word'], 
                $base_url . '/reposi/keyword/' . $keywords['kid']);
    } else {
      $keyws_art = $keyws_art . ', ' . l($keywords['k_word'],
                $base_url . '/reposi/keyword/' . $keywords['kid']);
    }
  }
  $markup = '<p>' . '<b>' . '<big>' . t($info_publi['ab_title']) . '</big>' .
            '</b>' .'</p>' . '<div>'. t('Publication details: ') . '</div>' . '<ul>' .
            '<li>'.'<i>'. t('Type: ') . '</i>' . $info_publi['ab_type'] .'</li>';
  $markup .= '<li>'.'<i>'.t('Author(s): ').'</i>'. $authors_art .'</li>';
  if (!empty($info_publi['ab_abstract'])) {
    $markup .= '<li>' . '<i>' . t('Abstract: ') . '</i>' . '<p align="justify">' .
               $info_publi['ab_abstract'] . '</p>' . '</li>';
  }
  if (!empty($keyws_art)) {
    $markup .= '<li>'.'<i>'.t('Keyword(s): ').'</i>'. $keyws_art .'</li>';
  }
  $markup .= '<li>'. '<i>'. t('Date: ') . '</i>' . $format_date . '</li>';
  if (!empty($info_publi_2['abd_url'])) {
    $markup .= '<li>'. '<i>'. t('URL: ') . '</i>' .
               l($info_publi_2['abd_url'], $info_publi_2['abd_url']) . '</li>';
  }
  if (!empty($info_publi_2['abd_doi'])) {
    $markup .= '<li>'. '<i>'. t('DOI: ') . '</i>' . $info_publi_2['abd_doi'] . '</li>';
  }
  $markup .= '<br>';
  if (!empty($info_publi['ab_journal_editorial'])) {
    $markup .= '<li>'. '<i>'. t('Journal/Book: ') . '</i>' . $info_publi['ab_journal_editorial'] .
               '</li>' . '<ul type = square>';
    if (!empty($info_publi_2['abd_volume'])) {
      $markup .= '<li>'. '<i>'. t('Volume: ') . '</i>' . $info_publi_2['abd_volume'] . '</li>';
    }
    if (!empty($info_publi_2['abd_issue'])) {
      $markup .= '<li>'. '<i>'. t('Issue: ') . '</i>' . $info_publi_2['abd_issue'] . '</li>';
    }
    if (!empty($info_publi_2['abd_start_page'])) {
      $markup .= '<li>'. '<i>'. t('Start page: ') . '</i>' . $info_publi_2['abd_start_page'] . '</li>';
    }
    if (!empty($info_publi_2['abd_final_page'])) {
      $markup .= '<li>'. '<i>'. t('Final page: ') . '</i>' . $info_publi_2['abd_final_page'] . '</li>';
    }
    if (!empty($info_publi_2['abd_issn'])) {
      $markup .= '<li>'. '<i>'. t('ISSN: ') . '</i>' . $info_publi_2['abd_issn'] . '</li>';
    }
    $markup .= '</ul>';
  }
  $markup .= '</ul>';
  $form['body'] = array('#markup' => $markup);
  $search_id = db_select('reposi_publication', 'p');
  $search_id->fields('p', array('pid'))
                ->condition('p.p_abid', $art_id, '=');
  $id_publication = $search_id->execute()->fetchField();
  variable_set('publication_id',$id_publication);
  $form['export'] = array(
    '#type' => 'item',
    '#title' => t('Export formats: '),
    '#markup' => l(t('RIS'), $base_url . '/reposi/ris/' . $id_publication),
  );
  return $form;

}
}

