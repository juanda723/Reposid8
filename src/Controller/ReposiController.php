<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\reposi\Controller;

use Drupal\Core\Database;

class ReposiController {
  public function PubliListReposiSearch() {

  global $base_url;
  $words = \Drupal::routeMatch()->getParameter('node');

  $inventory=' ';

  $each_word = explode('-', $words);
  $num_words = count($each_word);
  $complete = implode(' ', $each_word);
  $search_compl = db_select('reposi_publication', 'p');
  $search_compl->fields('p')
              ->condition('p.p_check', 1, '=')
              ->condition('p.p_title', $complete, '=');
  $search_cpl = $search_compl->execute();
  $search_cpl -> allowRowCount = TRUE;
  $somethig_cpl = $search_cpl->rowCount();
  $pids = array();
  if ($somethig_cpl <> 0) {
    foreach ($search_cpl as $ids_cpl) {
      $pids[] = $ids_cpl->pid;
    }
  }
  $search_by_char = db_select('reposi_publication', 'p');
  $search_by_char->fields('p')
              ->condition('p.p_check', 1, '=');
  $search_char = $search_by_char->execute();
  foreach ($search_char as $characters) {
    $title_bit = explode(' ', $characters->p_title);
    foreach ($title_bit as $search_word) {
      for ($i=1; $i <= $num_words; $i++) {
        $a = $i-1;
        $new_search_word = strtolower($search_word);
        $new_each_word = strtolower($each_word[$a]);
        $new_search_w = reposi_string($new_search_word);
        $new_each_w = reposi_string($new_each_word);
        if ($new_search_w == $new_each_w) {
          $pids[] = $characters->pid;
      }
    }
  }
}
  $new_pids = array_unique($pids);
  $publications = '';
  foreach ($new_pids as $ids) {
    $search_publi = db_select('reposi_publication', 'p')->extend('PagerDefault');
    $search_publi->fields('p')
                ->condition('p.pid', $ids, '=')
                ->orderBy('p.p_year', 'DESC')
                ->limit(10);
    $list_pub = $search_publi->execute();
    foreach ($list_pub as $list_p) {
      $pub_type = $list_p->p_type;
      $pub_title = $list_p->p_title;
      $pub_year = $list_p->p_year;
      $tsid = $list_p->p_tsid;
      $abid = $list_p->p_abid;
      if (isset($abid)) {
        $search_p_a = db_select('reposi_publication_author', 'pa');
        $search_p_a->fields('pa', array('ap_author_id', 'ap_abid'))
                   ->condition('pa.ap_abid', $abid, '=');
        $p_a = $search_p_a->execute();
        $list_aut_abc='';
        foreach ($p_a as $art_aut) {
          $search_aut = db_select('reposi_author', 'a');
          $search_aut->fields('a')
                     ->condition('a.aid', $art_aut->ap_author_id, '=');
          $each_aut = $search_aut->execute()->fetchAssoc();
          $f_name = reposi_string($each_aut['a_first_name']);
          if (!empty($each_aut['a_second_name'])) {
            $s_name = reposi_string($each_aut['a_second_name']);
            $list_aut_abc = $list_aut_abc . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                          ' ' . $f_name[0] . '. ' . $s_name[0] . '.',
                          $base_url . '/reposi/author/' . $art_aut->ap_author_id) . ', ';
          } else {
            $list_aut_abc = $list_aut_abc . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                          ' ' . $f_name[0] . '.', $base_url . '/reposi/author/' . $art_aut->ap_author_id) . ', ';
          }
        }
        if ($pub_type == 'Article') {
          $publications = $publications .'<p>'. $list_aut_abc.'(' . $pub_year . ') ' .'<b>'. l($pub_title,
                          $base_url . '/reposi/article/' . $abid . '/free') . '</b>' . '.' . '<br>' .
                          '<small>' . t('Export formats: ') .
                          l(t('RIS'), $base_url . '/reposi/ris/' . $list_p->pid) . '</small>' . '</p>';
        } elseif ($list_p->p_type == 'Book'){
          $publications .= '<p>'. $list_aut_abc.'(' . $pub_year . ') ' .'<b>'. l($pub_title,
                          $base_url . '/reposi/book/' . $abid . '/free') . '</b>' . '.' . '<br>' .
                          '<small>' . t('Export formats: ') .
                          l(t('RIS'), $base_url . '/reposi/ris/' . $list_p->pid) . '</small>' . '</p>';
        } else {
          $publications .= '<p>'. $list_aut_abc.'(' . $pub_year . ') ' .'<b>'.
                          l($pub_title, $base_url . '/reposi/chap_book/' . $abid . '/free') . '</b>' .
                          '.' . '<br>' . '<small>' . t('Export formats: ') .
                          l(t('RIS'), $base_url . '/reposi/ris/' . $list_p->pid) . '</small>' . '</p>';
        }
      } elseif (isset($tsid)) {
        $search_p_a = db_select('reposi_publication_author', 'pa');
        $search_p_a->fields('pa', array('ap_author_id', 'ap_tsid'))
                   ->condition('pa.ap_tsid', $tsid, '=');
        $p_a = $search_p_a->execute();
        $list_aut_ts='';
        foreach ($p_a as $the_aut) {
          $search_aut = db_select('reposi_author', 'a');
          $search_aut->fields('a')
                     ->condition('a.aid', $the_aut->ap_author_id, '=');
          $each_aut = $search_aut->execute()->fetchAssoc();
          $f_name = reposi_string($each_aut['a_first_name']);
          if (!empty($each_aut['a_second_name'])) {
            $s_name = reposi_string($each_aut['a_second_name']);
            $list_aut_ts = $list_aut_ts . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                          ' ' . $f_name[0] . '. ' . $s_name[0] . '.',
                          $base_url . '/reposi/author/' . $the_aut->ap_author_id) . ', ';
          } else {
            $list_aut_ts = $list_aut_ts . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                          ' ' . $f_name[0] . '.', $base_url . '/reposi/author/' . $the_aut->ap_author_id) . ', ';
          }
        }
        if ($pub_type == 'Thesis') {
          $publications .= '<p>'. $list_aut_ts. '(' . $pub_year . ') ' .'<b>'. l($pub_title,
                          $base_url . '/reposi/thesis/' . $tsid . '/free') . '</b>' . '.' . '<br>' .
                          '<small>' . t('Export formats: ') .
                          l(t('RIS'), $base_url . '/reposi/ris/' . $list_p->pid) . '</small>' . '</p>';
        } else {
          $publications .= '<p>'. $list_aut_ts. '(' . $pub_year . ') ' .'<b>'. l($pub_title,
                          $base_url . '/reposi/software/' . $tsid . '/free') . '</b>' . '.' . '<br>' .
                          '<small>' . t('Export formats: ') .
                          l(t('RIS'), $base_url . '/reposi/ris/' . $list_p->pid) . '</small>' . '</p>';
        }
      } else {
        $cpid = $list_p->p_cpid;
        $search_p_a = db_select('reposi_publication_author', 'pa');
        $search_p_a->fields('pa', array('ap_author_id', 'ap_cpid'))
                   ->condition('pa.ap_cpid', $cpid, '=');
        $p_a = $search_p_a->execute();
        $list_aut_cp='';
        foreach ($p_a as $con_aut) {
          $search_aut = db_select('reposi_author', 'a');
          $search_aut->fields('a')
                     ->condition('a.aid', $con_aut->ap_author_id, '=');
          $each_aut = $search_aut->execute()->fetchAssoc();
          $f_name = reposi_string($each_aut['a_first_name']);
          if (!empty($each_aut['a_second_name'])) {
            $s_name = reposi_string($each_aut['a_second_name']);
            $list_aut_cp = $list_aut_cp . l($each_aut['a_first_lastname'] . ' ' .
                          $each_aut['a_second_lastname'] . ' ' . $f_name[0] . '. ' . $s_name[0] . '.',
                          $base_url . '/reposi/author/' . $con_aut->ap_author_id) . ', ';
          } else {
            $list_aut_cp = $list_aut_cp . l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                          ' ' . $f_name[0] . '.', $base_url . '/reposi/author/' . $con_aut->ap_author_id) . ', ';
          }
        }
        if ($pub_type == 'Conference') {
          $publications .= '<p>'.$list_aut_cp . '(' . $pub_year . ') ' .'<b>'.
                          l($pub_title, $base_url . '/reposi/conference/' . $cpid . '/free') .
                          '</b>' . '.' . '<br>' . '<small>' . t('Export formats: ') .
                          l(t('RIS'), $base_url . '/reposi/ris/' . $list_p->pid) . '</small>' . '</p>';
        } else {
          $publications .= '<p>'.$list_aut_cp . '(' . $pub_year . ') ' .'<b>'.
                    l($pub_title, $base_url . '/reposi/patent/' . $cpid . '/free') . '</b>' . '.' . '<br>' .
                    '<small>' . t('Export formats: ') .
                    l(t('RIS'), $base_url . '/reposi/ris/' . $list_p->pid) . '</small>' . '</p>';
        }
      }
    }
  }
  if (empty($publications)) {
    $publications .= '<p>'. 'No matches'. '</p>';
  }



 return array(
      '#type' => 'markup',
      '#markup' => t($publications)
    );

 /* $pids = array();
  if ($somethig_cpl <> 0) {
    foreach ($search_cpl as $ids_cpl) {
      $pids[] = $ids_cpl->pid;
    }
  }
*/

}

/*public function ListAuthor() {
    return array(
      '#type' => 'markup',
      '#markup' => t('Hello, World!'),
    );
  }*/

  function ListAuthor() {
  global $base_url;
  $search_aut = db_select('reposi_author', 'a')->extend('Drupal\Core\Database\Query\PagerSelectExtender');
  $search_aut->fields('a')
              ->orderBy('a_first_lastname', 'ASC')
              ->limit(25);
  $authors = $search_aut->execute();
  $flag_aut=0;
  foreach ($authors as $keyw) {
  	$flag_aut++;
  	if ($flag_aut == 1) {
      $author = '<li>'. l($keyw->a_first_lastname . ' ' . $keyw->a_second_lastname .
                  ' ' . $keyw->a_first_name . ' ' . $keyw->a_second_name . ' ',
                  $base_url . '/reposi/author/' . $keyw->aid) . '</li>';
    } else {
      $author = $author . '<li>'.
                 l($keyw->a_first_lastname . ' ' . $keyw->a_second_lastname . ' ' .
                  $keyw->a_first_name . ' ' . $keyw->a_second_name . ' ',
                  $base_url . '/reposi/author/' . $keyw->aid) . '</li>';
    }
  }
  if (empty($author)) {
    $display_aut =  'Without authors';
  } else {
    $display_aut = $author;
  }
  $markup = '<div>' . '</div>' . '<ul>' . $display_aut . '</ul>';
  return array(
      '#type' => 'markup',
      '#markup' => t($markup),
    );
  }

  // CIERRA LA CLASE
  }
