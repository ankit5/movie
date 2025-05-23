<?php

namespace Drupal\domain_alias;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a domain alias entity.
 */
interface DomainAliasInterface extends ConfigEntityInterface {

  /**
   * Get the matching pattern value for an alias record.
   *
   * @return string
   *   The matching pattern.
   */
  public function getPattern();

  /**
   * Get the numeric domain_id value for an alias record.
   *
   * @return int
   *   The domain id for the alias record.
   */
  public function getDomainId();

  /**
   * Get the parent domain entity for an alias record.
   *
   * @return \Drupal\domain\Entity\Domain
   *   The parent domain for the alias record or NULL if not set.
   */
  public function getDomain();

  /**
   * Get the redirect value (301|302|NULL) for an alias record.
   *
   * @return int|null
   *   The redirect value.
   */
  public function getRedirect();

  /**
   * Get the current environment of the request.
   *
   * Aliases map be mapped to an environment property, which lets a group of
   * aliases act as "peers" and always set URLs relative to each other.
   *
   * @return string
   *   The matching environment.
   */
  public function getEnvironment();

}
