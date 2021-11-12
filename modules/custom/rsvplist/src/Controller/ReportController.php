<?php

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Database\Database;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Html;

/**
 * Controller for RSVP List Report.
 */
class ReportController extends ControllerBase {

  /**
   * Gets all RSVPs for all nodes.
   *
   * @return array
   */
  protected function load() {
    $select = Database::getConnection()->select('rsvplist', 'r');
    // Join the users table, so we can get the entry creator's username.
    $select->join('users_field_data', 'u', 'r.uid = u.uid');
    // Join the node table, so we can get the event's name.
    $select->join('node_field_data', 'n', 'r.nid = n.nid');
    // Select these specific fields for the output.
    $select->addField('u', 'name', 'username');
    $select->addField('n', 'title');
    $select->addField('r', 'mail');
    $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
    return $entries;
  }

  /**
   * Creates the report page.
   *
   * @return array
   *   Render array for report output
   */
  public function report() {
    $content = [];
    $content['message'] = [
      '#markup' => $this->t('below is a list of all Event RSVPs including username, email addres and the name of the event they will be attending.'),
    ];
    $headers = [
      t('Name'),
      t('Event'),
      t('Email'),
    ];
    $rows = [];

    foreach ($entries = $this->load() as $entry) {
      $obj_html = new Html();

      // Sanitize each entry.
      $rows[] = array_map(function ($entry) use ($obj_html) {

          return $obj_html::escape($entry);

      }, $entry);
    }

    $content['table'] = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => t('No entries available.'),
    ];
    // Don't cache this page.
    $content['#cache']['max-age'] = 0;
    return $content;

  }

}
