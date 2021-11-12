<?php

namespace Drupal\rsvplist\Form;

use Drupal\user\Entity\User;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an RSVP Email form.
 */
class RSVPForm extends FormBase {

  /**
   * (@inheritdoc)
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  /**
   * (@inheritdoc)
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');

    if (isset($node)) {
      $nid = $node->id();
    }

    $form['email'] = [
      '#title' => t('Email Address'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t("We'll send updates to the email address your provide."),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];

    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  /**
   * (@inheritdoc)
   */
  public function validadeForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if ($value == !\Drupal::service('email.validator')->isValid($value)) {
      $form_state->setErrorByName('email', t('The email addres %mail is not valid.', ['%mail' => $value]));
      return;
    }
    $node = \Drupal::routeMatch()->getParameter('node');
    // Check if email already is set for this node.
    $select = Database::getConnection()->select('rsvplist', 'r');
    $select->fields('r', ['nid']);
    $select->condition('nid', $node->id());
    $select->condition('mail', $value);
    $results = $select->execute();
    if (!empty($results->fetchCol())) {
      // We found a row with this nid and email.
      $form_state->setErrorByName('email', t('The address %mail is already subscribed to this list.', ['%mail' => $value]));
    }
  }

  /**
   * (@inheritdoc)
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = User::load(\Drupal::currentUser()->id());
    \Drupal::database()->insert('rsvplist')->fields([
      'mail' => $form_state->getValue('email'),
      'nid' => $form_state->getValue('nid'),
      'uid' => $user->id(),
      'created' => time(),
    ])
      ->execute();
    \Drupal::messenger()->t('Thank for your RSVP, you are on the list for the event');
  }

}
