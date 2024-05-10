<?php

namespace Drupal\views_alias_filter\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\StringFilter;
use Drupal\Core\Database\Query\Condition;

/**
 * Basic "URL alias filter".
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("url_alias_filter")
 */
class UrlAliasFilter extends StringFilter {

  /**
   * The select query for getting nids from the "path_alias" table.
   *
   * @var Drupal\Core\Database\Query\Select
   */
  protected $queryPaths;

  /**
   * Add this filter to the query.
   */
  public function query() {
    $this->ensureMyTable();

    $info = $this->operators();
    if (empty($info[$this->operator]['method'])) {
      return;
    }

    $this->queryPaths = $this->connection->select('path_alias', 'p');
    $this->queryPaths->addField('p', 'path');
    $this->{$info[$this->operator]['method']}('p.alias');

    $paths = $this->queryPaths->execute()->fetchAllKeyed(0, 0);
    $nids = [];

    foreach ($paths as $path) {
      // Extract nid from the path string by removing the '/node/' part.
      $nids[] = str_replace('/node/', '', $path);
    }

    if ($nids) {
      $field = "$this->tableAlias.$this->realField";
      $this->query->addWhere($this->options['group'], $field, $nids, 'IN');
    }
    else {
      // If aliases was not found then add the condition to return nothing.
      $this->query->addWhere($this->options['group'], (new Condition('AND'))->alwaysFalse());
    }
  }

  /**
   * Add the condition for opEqual operator.
   */
  public function opEqual($field) {
    $this->queryPaths->condition($field, $this->value, $this->operator());
  }

  /**
   * Add the condition for opContains operator.
   */
  protected function opContains($field) {
    $operator = $this->getConditionOperator('LIKE');
    $this->queryPaths->condition($field, '%' . $this->connection->escapeLike($this->value) . '%', $operator);
  }

  /**
   * Add the condition for opContainsWord operator.
   */
  protected function opContainsWord($field) {
    $where = $this->operator == 'word' ? $this->query->getConnection()->condition('OR') : $this->query->getConnection()->condition('AND');

    // Don't filter on empty strings.
    if (empty($this->value)) {
      return;
    }

    preg_match_all(static::WORDS_PATTERN, ' ' . $this->value, $matches, PREG_SET_ORDER);
    $operator = $this->getConditionOperator('LIKE');
    foreach ($matches as $match) {
      $phrase = FALSE;
      // Strip off phrase quotes.
      if ($match[2][0] == '"') {
        $match[2] = substr($match[2], 1, -1);
        $phrase = TRUE;
      }
      $words = trim($match[2], ',?!();:-');
      $words = $phrase ? [$words] : preg_split('/ /', $words, -1, PREG_SPLIT_NO_EMPTY);
      foreach ($words as $word) {
        $where->condition($field, '%' . $this->connection->escapeLike(trim($word, " ,!?")) . '%', $operator);
      }
    }

    if ($where->count() === 0) {
      return;
    }

    $this->queryPaths->condition($where);
  }

  /**
   * Add the condition for opStartsWith operator.
   */
  protected function opStartsWith($field) {
    $operator = $this->getConditionOperator('LIKE');
    $this->queryPaths->condition($field, $this->connection->escapeLike($this->value) . '%', $operator);
  }

  /**
   * Add the condition for opNotStartsWith operator.
   */
  protected function opNotStartsWith($field) {
    $operator = $this->getConditionOperator('NOT LIKE');
    $this->queryPaths->condition($field, $this->connection->escapeLike($this->value) . '%', $operator);
  }

  /**
   * Add the condition for opEndsWith operator.
   */
  protected function opEndsWith($field) {
    $operator = $this->getConditionOperator('LIKE');
    $this->queryPaths->condition($field, '%' . $this->connection->escapeLike($this->value), $operator);
  }

  /**
   * Add the condition for opNotEndsWith operator.
   */
  protected function opNotEndsWith($field) {
    $operator = $this->getConditionOperator('NOT LIKE');
    $this->queryPaths->condition($field, '%' . $this->connection->escapeLike($this->value), $operator);
  }

  /**
   * Add the condition for opNotLike operator.
   */
  protected function opNotLike($field) {
    $operator = $this->getConditionOperator('NOT LIKE');
    $this->queryPaths->condition($field, '%' . $this->connection->escapeLike($this->value) . '%', $operator);
  }

  /**
   * Add the condition for opShorterThan operator.
   */
  protected function opShorterThan($field) {
    $placeholder = $this->placeholder();
    // Type cast the argument to an integer because the SQLite database driver
    // has to do some specific alterations to the query base on that data type.
    $this->queryPaths->where("LENGTH($field) < $placeholder", [$placeholder => (int) $this->value]);
  }

  /**
   * Add the condition for opLongerThan operator.
   */
  protected function opLongerThan($field) {
    $placeholder = $this->placeholder();
    // Type cast the argument to an integer because the SQLite database driver
    // has to do some specific alterations to the query base on that data type.
    $this->queryPaths->where("LENGTH($field) > $placeholder", [$placeholder => (int) $this->value]);
  }

  /**
   * Add the condition for opRegex operator.
   */
  protected function opRegex($field) {
    $this->queryPaths->condition($field, $this->value, 'REGEXP');
  }

}
