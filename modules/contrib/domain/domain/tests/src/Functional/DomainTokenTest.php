<?php

namespace Drupal\Tests\domain\Functional;

use Drupal\Core\Session\AccountInterface;

/**
 * Tests the domain token handler.
 *
 * @group domain
 */
class DomainTokenTest extends DomainTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['domain', 'block'];

  /**
   * Tests the handling of an inbound request.
   */
  public function testDomainTokens() {
    // No domains should exist.
    $this->domainTableIsEmpty();

    // Create four new domains programmatically.
    $this->domainCreateTestDomains(4);

    // Since we cannot read the service request, we place a block
    // which shows the current domain token information.
    $this->drupalPlaceBlock('domain_token_block');

    // To get around block access, let the anon user view the block.
    user_role_grant_permissions(AccountInterface::ANONYMOUS_ROLE, ['view domain information']);

    // Test the response of the default home page.
    /** @var \Drupal\domain\DomainInterface $domain */
    foreach ($this->getDomains() as $domain) {
      $this->drupalGet($domain->getPath());
      $this->assertSession()->responseContains($domain->label());
      $this->assertSession()->responseContains('<th>Token</th>');
      foreach ($this->tokenList() as $token => $callback) {
        $this->assertSession()->responseContains("<td>$token</td>");
        // The URL token is sensitive to the path, which is /user, but that
        // does not come across when making the callback outside of a request
        // context.
        $value = call_user_func_array([$domain, $callback], []);
        if ($token === '[domain:url]') {
          $value = str_replace('user', '', $value);
          if (substr($value, -1) !== '/') {
            $value .= '/';
          }
        }
        $this->assertSession()->responseContains('<td>' . $value . '</td>');
      }
    }
  }

  /**
   * Gets the list of tokens and value callbacks used by the test.
   *
   * @return array
   *   An array keyed by token string, with value of expected domain value.
   */
  private function tokenList() {
    $tokens = [];
    foreach (\Drupal::service('domain.token')->getCallbacks() as $key => $callback) {
      $name = "[domain:$key]";
      $tokens[$name] = $callback;
    }
    return $tokens;
  }

}
