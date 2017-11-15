<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */

namespace Drupal\reposi\Controller;
use Drupal\Core\Database;
use Drupal\Core\Url;

class Reposi_info_publication {


public static function reposi_string($string) {
    $string = trim($string);
    $string = str_replace(
      array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
      array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
      $string
    );
    $string = str_replace(
      array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
      array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
      $string
    );
    $string = str_replace(
      array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
      array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
      $string
    );
    $string = str_replace(
      array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
      array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
      $string
    );
    $string = str_replace(
      array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
      array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
      $string
    );
    $string = str_replace(
      array('ñ', 'Ñ', 'ç', 'Ç'),
      array('n', 'N', 'c', 'C',),
      $string
    );
    return $string;
  }



public static function reposi_formt_date($day, $month, $year){
  if (empty($month)) {
    $month_letter = '';
  } else {
    if ($month == 1) {
      $month_letter = 'January';
    } elseif ($month == 2) {
      $month_letter = 'February';
    } elseif ($month == 3) {
      $month_letter = 'March';
    } elseif ($month == 4) {
      $month_letter = 'April';
    } elseif ($month == 5) {
      $month_letter = 'May';
    } elseif ($month == 6) {
      $month_letter = 'June';
    } elseif ($month == 7) {
      $month_letter = 'July';
    } elseif ($month == 8) {
      $month_letter = 'August';
    } elseif ($month == 9) {
      $month_letter = 'September';
    } elseif ($month == 10) {
      $month_letter = 'October';
    } elseif ($month == 11) {
      $month_letter = 'November';
    } else {
      $month_letter = 'December';
    }
  }
  if (empty($day) && empty($month)) {
    $format_dates = $year;
  } elseif (empty($day)) {
    $format_dates = $month_letter . '/' . $year;
  } else {
    $format_dates = $day . '/' . $month_letter . '/' . $year;
  }
  return $format_dates;
}

public function reposi_info_article_free(){

  $art_id = \Drupal::routeMatch()->getParameter('node');
  global $base_url;
  $form['pid'] = array(
    '#type' => 'value',
    '#value' => $art_id,
  );
  $search_art = db_select('reposi_article_book', 'ab');
  $search_art->fields('ab')
          ->condition('ab.abid', $art_id, '=');
  $info_publi = $search_art->execute()->fetchAssoc();
  $ejemplovar = print_r($info_publi['ab_title']);

  $search_art_detail = db_select('reposi_article_book_detail', 'abd');
  $search_art_detail->fields('abd')
          ->condition('abd.abd_abid', $art_id, '=');
  $info_publi_2 = $search_art_detail->execute()->fetchAssoc();
  $search_date = db_select('reposi_date', 'd');
  $search_date->fields('d')
              ->condition('d.d_abid', $art_id, '=');
  $art_date = $search_date->execute()->fetchAssoc();

  $format_date = Reposi_info_publication::reposi_formt_date($art_date['d_day'],$art_date['d_month'],$art_date['d_year']);
  $search_p_a_art = db_select('reposi_publication_author', 'pa');
  $search_p_a_art->fields('pa')
                ->condition('pa.ap_abid', $art_id, '=');
  $couple_art = $search_p_a_art->execute();
  $authors_art = '';
  $couple_art -> allowRowCount = TRUE;
  $num_aut = $couple_art->rowCount();
  $flag_aut = 0;
  foreach ($couple_art as $aut_art) {
    $flag_aut++;
    $search_aut = db_select('reposi_author', 'a');
    $search_aut->fields('a')
               ->condition('a.aid', $aut_art->ap_author_id, '=');
    $each_aut = $search_aut->execute()->fetchAssoc();
    if ($flag_aut <> $num_aut) {
      $f_name = Reposi_info_publication::reposi_string($each_aut['a_first_name']);
      if (!empty($each_aut['a_second_name'])) {
        $s_name = Reposi_info_publication::reposi_string($each_aut['a_second_name']);
        $authors_art = $authors_art . \Drupal::l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '. ' . $s_name[0] . '.',
                      $base_url . '/reposi/author/' . $aut_art->ap_author_id) . '.';
      } else {
        $authors_art = $authors_art . \Drupal::l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '. ',Url::fromRoute('reposi.reposi_format_ris',['node'=>$aut_art->ap_author_id])) . '.';
      }
    } else {
      $search_aut = db_select('reposi_author', 'a');
      $search_aut->fields('a')
                 ->condition('a.aid', $aut_art->ap_author_id, '=');
      $each_aut = $search_aut->execute()->fetchAssoc();
      $f_name = Reposi_info_publication::reposi_string($each_aut['a_first_name']);
      if (!empty($each_aut['a_second_name'])) {
        $s_name = Reposi_info_publication::reposi_string($each_aut['a_second_name']);
        $authors_art = $authors_art . \Drupal::l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '. ' . $s_name[0] . '.',Url::fromRoute('reposi.reposi_format_ris',['node'=>$aut_art->ap_author_id])) . '.';
/////////////////////////Duireccion CAmbiar Falta /reposi/author/{node}
      } else {
        $authors_art = $authors_art . \Drupal::l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '. ',Url::fromRoute('reposi.reposi_format_ris',['node'=>$aut_art->ap_author_id])) . '.';
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
      $keyws_art = \Drupal::l($keywords['k_word'],Url::fromRoute('reposi.reposi_format_ris',['node'=>$keywords['kid']]));
      ///////////////////////Falta direccion a /reposi/keyword/{node} ESta en ris

    } else {
      $keyws_art = $keyws_art . ', ' . $keyws_art = \Drupal::l($keywords['k_word'],Url::fromRoute('reposi.reposi_format_ris',['node'=>$keywords['kid']]));
    }
  }
  //$info_publi['ab_title']='1';
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
//variable_set('publication_id',$id_publication);

\Drupal::state()->set('publication_id', $id_publication);
if(empty($id_publication)){
$id_publication=t("Error");
}

$url=Url::fromRoute('reposi.reposi_format_ris',['node'=>$id_publication]);
$risSend=\Drupal::l(t('RIS'),$url);
  $form['export'] = array(
    '#type' => 'item',
    '#title' => t('Export formats: '.$ejemplovar),
    '#markup' => $risSend,
  );
  return $form;
}

////////////////////////////////////////////////

function reposi_info_book_free(){
  $book_id = \Drupal::routeMatch()->getParameter('node');
  global $base_url;
  $form['pid'] = array(
    '#type' => 'value',
    '#value' => $book_id,
  );
  $search_book = db_select('reposi_article_book', 'ab');
  $search_book->fields('ab')
          ->condition('ab.abid', $book_id, '=');
  $info_publi = $search_book->execute()->fetchAssoc();
  $search_book_detail = db_select('reposi_article_book_detail', 'abd');
  $search_book_detail->fields('abd')
          ->condition('abd.abd_abid', $book_id, '=');
  $info_publi_2 = $search_book_detail->execute()->fetchAssoc();
  $search_date = db_select('reposi_date', 'd');
  $search_date->fields('d', array('d_year'))
              ->condition('d.d_abid', $book_id, '=');
  $book_year = $search_date->execute()->fetchField();

  $search_p_a_book = db_select('reposi_publication_author', 'pa');
  $search_p_a_book->fields('pa')
                ->condition('pa.ap_abid', $book_id, '=');
  $couple_book = $search_p_a_book->execute();
  $authors_book = '';
  $couple_book -> allowRowCount = TRUE;
  $num_aut = $couple_book->rowCount();
  $flag_aut = 0;
  foreach ($couple_book as $aut_book) {
    $flag_aut++;
    if ($flag_aut <> $num_aut) {
      $search_aut = db_select('reposi_author', 'a');
      $search_aut->fields('a')
                 ->condition('a.aid', $aut_book->ap_author_id, '=');
      $each_aut = $search_aut->execute()->fetchAssoc();
      $f_name = Reposi_info_publication::reposi_string($each_aut['a_first_name']);
      if (!empty($each_aut['a_second_name'])) {
        $s_name = Reposi_info_publication::reposi_string($each_aut['a_second_name']);
        $authors_book = $authors_book . \Drupal::l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '. ' . $s_name[0] . '.',Url::fromRoute('reposi.reposi_format_ris',['node'=>$aut_art->ap_author_id])) . ', ';
      } else {
        $authors_book = $authors_book . \Drupal::l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '.', Url::fromRoute('reposi.reposi_format_ris',['node'=>$aut_art->ap_author_id] )) . ', ';
      }
    } else {
      $search_aut = db_select('reposi_author', 'a');
      $search_aut->fields('a')
                 ->condition('a.aid', $aut_book->ap_author_id, '=');
      $each_aut = $search_aut->execute()->fetchAssoc();
      $f_name = reposi_string($each_aut['a_first_name']);
      if (!empty($each_aut['a_second_name'])) {
        $s_name = reposi_string($each_aut['a_second_name']);
        $authors_book = $authors_book . \Drupal::l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '. ' . $s_name[0] . '.',
                      Url::fromRoute('reposi.reposi_format_ris',['node'=>$aut_art->ap_author_id])) . '.';
      } else {
        $authors_book = $authors_book . \Drupal::l($each_aut['a_first_lastname'] . ' ' . $each_aut['a_second_lastname'] .
                      ' ' . $f_name[0] . '.', Url::fromRoute('reposi.reposi_format_ris',['node'=>$aut_art->ap_author_id])) . '.';
      }
    }
  }
  $markup = '<p>' . '<b>' . '<big>' . t($info_publi['ab_title']) . '</big>' .
            '</b>' . '</p>' . '<div>'. t('Publication details: ') . '</div>' . '<ul>' .
            '<li>'.'<i>'. t('Type: ') . '</i>' . $info_publi['ab_type'] .'</li>';
  if (!empty($info_publi['ab_subtitle_chapter'])) {
    $markup .= '<li>'.'<i>'.t('Subtitle: ').'</i>'. $info_publi['ab_subtitle_chapter']. '</li>';
  }
  $markup .= '<li>'.'<i>'.t('Author(s): ').'</i>'. $authors_book .'</li>';
  if (!empty($info_publi['ab_abstract'])) {
    $markup .= '<li>' . '<i>' . t('Description: ') . '</i>' . '<p align="justify">' .
               $info_publi['ab_abstract'] . '</p>' . '</li>';
  }
  $markup .= '<li>'. '<i>'. t('Publication’s year: ') . '</i>' . $book_year . '</li>';
  if (!empty($info_publi['ab_language'])) {
    $markup .= '<li>'. '<i>'. t('Language: ') . '</i>' . $info_publi['ab_language'] . '</li>';
  }
  if (!empty($info_publi_2['abd_volume'])) {
    $markup .= '<li>'. '<i>'. t('Volume: ') . '</i>' . $info_publi_2['abd_volume'] . '</li>';
  }
  if (!empty($info_publi_2['abd_issue'])) {
    $markup .= '<li>'. '<i>'. t('Issue: ') . '</i>' . $info_publi_2['abd_issue'] . '</li>';
  }
  if (!empty($info_publi['ab_journal_editorial'])) {
    $markup .= '<li>'. '<i>'. t('Publisher: ') . '</i>' . $info_publi['ab_journal_editorial'] . '</li>';
  }
  if (!empty($info_publi['ab_publisher'])) {
    $markup .= '<li>'. '<i>'. t('Publisher name: ') . '</i>' . $info_publi['ab_publisher'] . '</li>';
  }
  if (!empty($info_publi['ab_place'])) {
    $markup .= '<li>'. '<i>'. t('Publication’s place: ') . '</i>' . $info_publi['ab_place'] . '</li>';
  }
  if (!empty($info_publi_2['abd_issn'])) {
    $markup .= '<li>'. '<i>'. t('ISSN: ') . '</i>' . $info_publi_2['abd_issn'] . '</li>';
  }
  if (!empty($info_publi_2['abd_isbn'])) {
    $markup .= '<li>'. '<i>'. t('ISBN: ') . '</i>' . $info_publi_2['abd_isbn'] . '</li>';
  }
  if (!empty($info_publi_2['abd_url'])) {
    $markup .= '<li>'. '<i>'. t('URL: ') . '</i>' .
               l($info_publi_2['abd_url'], $info_publi_2['abd_url']) . '</li>';
  }
  if (!empty($info_publi_2['abd_doi'])) {
    $markup .= '<li>'. '<i>'. t('DOI: ') . '</i>' . $info_publi_2['abd_doi'] . '</li>';
  }
  $markup .= '</ul>';
  $form['body'] = array('#markup' => $markup);
  $search_id = db_select('reposi_publication', 'p');
  $search_id->fields('p', array('pid'))
                ->condition('p.p_abid', $book_id, '=');
  $id_publication = $search_id->execute()->fetchField();
  \Drupal::state()->set('publication_id',$id_publication);
  if(empty($id_publication)){
  $id_publication=t("Error");
  }

  $url=Url::fromRoute('reposi.reposi_format_ris',['node'=>$id_publication]);
  $risSend=\Drupal::l(t('RIS'),$url);
  $form['export'] = array(
    '#type' => 'item',
    '#title' => t('Export formats: '),
    '#markup' => $risSend,
  );
  return $form;
}



}
