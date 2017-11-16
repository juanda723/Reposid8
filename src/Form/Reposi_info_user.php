<?php
namespace Drupal\reposi\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityConfirmFormBase;
/**
 * Implements an example form.
 */
class Reposi_info_user extends FormBase {

  protected $id;

  public function getFormId() {
    return 'reposiinfouser_form';
  }

  public function getQuestion() {
    return t('Do you want to delete %id?', array('%id' => $this->id));
  }

    public function getCancelUrl() {
      return new Url('reposi.admuser');
  }

  public function getDescription() {
    return t('Only do this if you are sure!');
  }

  public function getConfirmText() {
    return t('Delete it!');
  }

  public function getCancelText() {
    return t('Nevermind');
  }

  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
  $id = NULL;
  $this->id = $id;
  /*$storage = $form_state->getStorage();
  $form_state->setStorage($storage);
  if (isset($form_state->getStorage('ask_confirm') {
    $form_confirm = confirm_form($form, 
        t('Delete user.'), 
        'reposi.listus',
        $description =t('Do you want delete this user?'), 
        t('Accept'), 
        t('Cancel'));
    return $form_confirm;
  } else */{
   $arg=\Drupal::routeMatch()->getParameter('node');
  	$serch_u = db_select('reposi_user', 'u');
    $serch_u->fields('u')
            ->condition('u.uid', $arg, '=');
    $serch_user = $serch_u->execute()->fetchField();
    $info_user = $serch_u->execute()->fetchAssoc(); 
    \Drupal::state()->set('info_user2', $info_user); 
    //variable_set('info_user2',$info_user);
    $search_stat = db_select('reposi_state', 's');
  	$search_stat->fields('s', array('s_type'))
                  	 ->condition('s.s_uid', $serch_user, '=');
  	$state = $search_stat->execute()->fetchField(); 
  	$search_aca_rol = db_select('reposi_academic', 'a');
  	$search_aca_rol->fields('a', array('academic_type'))
                     ->condition('a.academic_uid', $serch_user, '=');
  	$aca_rol = $search_aca_rol->execute()->fetchField();
    $form['uid'] = array(
  		'#type' => 'value',
  		'#value' => $arg,
  	);
    $markup = '<p>' . '<b>' . '<big>' . $info_user['u_first_name'] . ' ' . 
              $info_user['u_first_lastname'] . '</big>' . '</b>' . '</p>' . '<ul>' .
    			    '<li>' . '<i>' . t('ID: ') . '</i>'. $info_user['uid'] . '</li>' .
              '<li>' . '<i>' . t('Name: ') . '</i>' . $info_user['u_first_name']. ' ' .
              $info_user['u_second_name'] .'</li>' .
              '<li>'.'<i>'.t('Last name: ').'</i>'.$info_user['u_first_lastname']. ' ' .
              $info_user['u_second_lastname'].'</li>';
    if (!empty($info_user['u_affiliation'])) {
      $markup .= '<li>'. '<i>'. t('Affiliation: ') . '</i>' . 
          $info_user['u_affiliation'] .'</li>';
    }
    $markup .= '<li>'. '<i>'. t('Email 1: ') . '</i>' . 
        $info_user['u_email'] .'</li>';
    if (!empty($info_user['u_optional_email_1'])) {
      $markup .= '<li>'. '<i>'. t('Email 2: ') . '</i>' . 
          $info_user['u_optional_email_1'] .'</li>';
    }
    if (!empty($info_user['u_optional_email_2'])) {
      $markup .= '<li>'. '<i>'. t('Email 3: ') . '</i>' . 
          $info_user['u_optional_email_2'] .'</li>';
    }
    if (!empty($info_user['u_id_homonymous'])) {
      $markup .= '<li>'. '<i>'. t('ORCID: ') . '</i>' . 
          $info_user['u_id_homonymous'] .'</li>';
    }
    if (!empty($info_user['u_id_scopus'])) {
      $markup .= '<li>'. '<i>'. t('Scopus ID Author: ') . '</i>' . 
          $info_user['u_id_scopus'] .'</li>';
    }
    $markup .= '<li>'. '<i>'. t('State: ') . '</i>' . $state .'</li>' .
              '<li>'. '<i>'. t('Academic rol: ') . '</i>' . $aca_rol .'</li>' . '</ul>';          
    $form['body'] = array('#markup' => $markup);
    $form['edit'] = array(
      '#type' => 'submit',
      '#value' => t('Edit'),
      '#submit' => array('reposi_user_edit'),
    );
    $form['deactivate'] = array(
      '#type' => 'submit',
      '#value' => t('Deactivate'),
      '#submit' => array('reposi_user_deactivate'),
    );
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
    );
    return $form;
  }
  }
  
  public function reposi_user_edit($form, &$form_state){
	global $base_url;
        $form_state->setRedirect('reposi.listusactive', $form_state->getValue('uid'));
	//$form_state['redirect'] = $base_url . '/reposi/adm_edituser/' . $form_state['values']['uid'];
  }

  public function reposi_user_deactivate($form, &$form_state){
	db_update('reposi_state')->fields(array(
        's_type'   => 'Inactive',
  ))->condition('s_uid', $form_state->getValue('uid'))
  ->execute();
  drupal_set_message('User is update to inactive');
  }
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) 
  {
  
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    mymodule_delete($this->id);
  }
 
}
?>
