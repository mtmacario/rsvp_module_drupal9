<?php

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides an 'RSVP' List Block.
 *
 * @Block(
 *   id = "rsvp_block",
 *   admin_lael = @Translation("RSVP Block"),
 * )
 */
class RSVPBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {

    $node = \Drupal::routeMatch()->getParameter('node');

    $nid = NULL;
    /** @var \Drupal\rsvplist\EnablerService $enabler\ */
    $enabler = \Drupal::service('rsvplist.enabler');
    if ($node) {
      if ($enabler->isEnabled()) {

        return AccessResult::allowedIfHasPermission($account, 'view rsvplist');

      }
    }

    return AccessResult::forbidden();
  }

}
