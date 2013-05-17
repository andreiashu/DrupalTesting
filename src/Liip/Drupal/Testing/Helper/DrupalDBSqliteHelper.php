<?php

/**
 * Drupal Sqlite DB Helper
 * Manages active connections to an sqlite DB
 */

namespace Liip\Drupal\Testing\Helper;

class DrupalDBSqliteHelper {

  /**
   * @var array
   * List of DB handlers
   */
  private static $handlers = array();

  /**
   * @var string
   * Current active handler
   */
  private static $activeHandler;

  public function __construct() {}

  /**
   * @param null $handler_name
   * @param array $options
   */
  public static function activate($handler_name = NULL, $options = array()) {
    if (empty($handler_name)) {
      $handler_name = uniqid('', TRUE);
    }

    if (!isset(self::$handlers[$handler_name])) {
      self::initialise($handler_name, $options);
    }

    db_set_active($handler_name);
    self::setActiveHandler($handler_name);
  }

  /**
   * @param $handler_name
   * @param $db_options
   * @return mixed
   */
  public static function initialise($handler_name, $db_options) {
    $db_info = $db_options + array(
      'database' => '/tmp/' . uniqid('drupal_sqlite_', TRUE) . '.db',
      'driver' => 'sqlite',
    );
    \Database::addConnectionInfo($handler_name, 'default', $db_info);

    self::$handlers[$handler_name] = $db_info;

    return $db_info;
  }

  /**
   * @param $handler_name
   */
  public static function destroy($handler_name = NULL) {
    if (!isset($handler_name)) {
      $handler_name = self::getActiveHandler();
    }

    $db_info = self::getHandlerDBInfo($handler_name);
    db_close(array('target' => 'default'));
    unlink($db_info['database']);
    unset(self::$handlers[$handler_name]);
  }

  public static function getHandlerDBInfo($handler_name) {
    return self::$handlers[$handler_name];
  }

  /**
   *
   */
  public static function destroyAll() {
    foreach(array_keys(self::$handlers) as $handler_name) {
      self::destroy($handler_name);
    }
  }

  /**
   * @param $handler_name
   */
  public static function setActiveHandler($handler_name) {
    self::$activeHandler = $handler_name;
  }

  /**
   * @return string
   */
  public static function getActiveHandler() {
    return self::$activeHandler;
  }
}
