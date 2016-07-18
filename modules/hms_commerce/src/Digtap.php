<?php

namespace Drupal\hms_commerce;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Digtap drupal service class.
 */
class Digtap {

  private $configFactory;
  private $config;

  const PRICE_CATEGORY_API_PATH = '/home/de/api/v1/products';
  const DIGTAP_WIDGET_JS_PATH = '/digtap/widgets.min.js';

  /**
   * Digtap constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory from the container.
   */
  function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
    $this->config = $config_factory->get('hms_commerce.settings');
  }

  public function setApiUrl($url) {
    $this->configFactory->getEditable('hms_commerce.settings')
      ->set('api_source', $url)->save();
    // Refresh config object after making changes.
    $this->config = $this->configFactory->get('hms_commerce.settings');
  }

  public function getBaseApiUrl($display_error = FALSE) {
    $api_url = $this->config->get('api_source');
    if (!empty($api_url)) {
      return ($api_url);
    }
    // Warn administrative user if the API URL is not set.
    elseif ($display_error) {
      $message = "For products to display, the API source URL needs to be set <a href='@url'>here</a>.";
      self::registerError($message, ['@url' => $GLOBALS['base_url'] . "/admin/config/hmscommerce"], 'status');
    }
    return '';
  }

  public function getApiUrl($type) {
    $base_url = $this->getBaseApiUrl(TRUE);
    if (!empty($base_url)) {
      switch($type) {
        case 'price_category':
          return $base_url . self::PRICE_CATEGORY_API_PATH;
        case 'digtap_widgets':
          return $base_url . self::DIGTAP_WIDGET_JS_PATH;
      }
    }
    return '';
  }

  /**
   * Logs error and displays it to the privileged user.
   *
   * @param $message
   *  Untranslated message.
   * @param array $substitutions (optional)
   *  Substitutions (placeholder => substitution) which will replace placeholders
   *  with strings.
   * @param string $display (optional)
   *  Message type (status/warning/error), if set, message is displayed to
   *  privileged user accordingly.
   */
  public static function registerError($message, $substitutions = [], $display = NULL) {
    $message = strtr(t($message), $substitutions);
    \Drupal::logger('hms_commerce')->notice($message);
    if (!empty($display) && \Drupal::currentUser()->hasPermission('administer hms_commerce settings')) {
      drupal_set_message($message, $display);
    }
  }
}
