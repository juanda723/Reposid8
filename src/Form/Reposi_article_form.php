<?php

namespace Drupal\reposi\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Query;

/**
 * Implements an example form.
 */
class Reposi_article_form extends FormBase {

  public function getFormId() {
    return 'AddArticle_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

/*ISSET: Determina si una variable está definida y no es NULL.
$form_state['storage']: Los datos colocados en el contenedor de almacenamiento de la colección $ form_state se almacenarán automáticamente en caché y se volverán a cargar cuando se envíe el formulario, permitiendo que su código acumule datos de paso a paso y lo procese en la etapa final sin ningún código adicional 
ESTÁ RETORNANDO EL VALOR DE 1 EN $form_state['storage']['author'] SI LA VARIABLE ESTÁ DEBIDAMENTE DECLARADA CON ANTERIORIDAD 
      $form_state['storage']['author'] = isset($form_state['storage']['author'])?
                                         $form_state['storage']['author']:1;*/

  global $_reposi_start_form;
  $markup = '<p>' . '<i>' . t('You must complete the required fields before the 
            add authors or keywords.') . '</i>' . '</p>';
  $form['body'] = array('#markup' => $markup);
  $form['title'] = array(
    '#title' => t('Title'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#maxlength' => 511,
  );
  $form['abstract'] = array(
    '#title' => t('Abstract'),
    '#type' => 'textarea',
  );

  $storage = $form_state->getStorage('author');
  isset($storage) ? $form_state->getStorage('author'):1;
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
  for ($i = 1; $i <= $form_state->getStorage('author'); $i++) {
  //$options[$i];
  $form['body'] = array('#markup' => 'hola');
  }
  //$form['body'] = array('#markup' => $storage);

   return $form;

  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }


// Llave que cierra la clase:--->
}
?>
