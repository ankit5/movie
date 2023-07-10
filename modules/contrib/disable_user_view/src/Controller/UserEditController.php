<?php

namespace Drupal\disable_user_view\Controller;

use Drupal\user\Controller\UserController;
use Drupal\user\UserInterface;

/**
 * Controller routines for user routes.
 */
class UserEditController extends UserController {

  /**
   * Redirects users to their profile edit page or the user you are trying to access.
   *
   * This controller assumes that it is only invoked for authenticated users.
   * This is enforced for the 'user.page' route with the '_user_is_logged_in'
   * requirement.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Returns a redirect to the profile edit form of the currently logged in
   *   user or the user you are trying to access.
   */
  public function userPage(UserInterface $user = NULL) {
    $uid = !empty($user) ? $user->id() : $this->currentUser()->id();
    return $this->redirect('entity.user.edit_form', ['user' => $uid]);
  }
}
