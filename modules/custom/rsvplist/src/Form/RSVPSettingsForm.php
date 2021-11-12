<?php

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form to configure RSVP List module settings.
 */
class RSVPSettingsForm extends ConfigFormBase {

  /**
   * {@interitdoc}
   */
  public function getFormID() {
    return 'rsvplist_admin_settings';
  }

  /**
   * {@interitdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'rsvplist.settings',
    ];
  }

  /**
   * {@interitdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $types = node_type_get_names();
    $config = $this->config('rsvplist.settings');
    $form['rsvplist_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('The content types to enable RSVP collection for'),
      '#default_value' => $config->get('allowed_types'),
      '#default_value' => $types,
      '#description' => t('On the specified node types, an RSVP option will be available and can be enable while
            that node is being edited'),
    ];
    $form['array_filter'] = ['#type' => 'value', '#value' => TRUE];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@interitdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $allowed_types = array_filter($form_state->getValue('rsvplist_types'));
    sort($allowed_types);
    $this->config('rsvplist.settings')
      ->set('allowed_types', $allowed_types)
      ->save();
    parent::submitForm($form, $form_state);
  }

}
