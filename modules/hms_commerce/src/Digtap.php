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
  const PREMIUM_CONTENT_JS_PATH = '/assets/premium-content.min.js';

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

  /**
   * Sets the Bestseller API url.
   *
   * @param string $url
   *  Absolute url string without trailing slash.
   */
  public function setBestsellerApiUrl($url) {
    $this->configFactory->getEditable('hms_commerce.settings')
      ->set('bestseller_url', $url)->save();
    // Refresh config object after making changes.
    $this->config = $this->configFactory->get('hms_commerce.settings');
  }

  /**
   * Gets the Bestseller API url.
   *
   * @param bool $display_error
   *  If TRUE and no URL is set, an error is logged and displayed to privileged
   *  user.
   *
   * @return string
   *  Returns the URL string or an empty string.
   */
  public function getBestsellerApiUrl($display_error = FALSE) {
    $bestseller_url = $this->config->get('bestseller_url');
    if (!empty($bestseller_url)) {
      return ($bestseller_url);
    }
    // Warn administrative user if the API URL is not set.
    elseif ($display_error) {
      $message = "For products to display, the Bestseller API URL needs to be set <a href='@url'>here</a>.";
      self::registerError($message, ['@url' => $GLOBALS['base_url'] . "/admin/config/hmscommerce"], 'warning');
    }
    return '';
  }

  /**
   * Gets the Usermanager API url from the hms module.
   *
   * @param bool $display_error
   *  If TRUE and no URL is set, an error is logged and displayed to privileged
   *  user.
   *
   * @return string
   *  Returns the URL string or an empty string.
   */
  private function getUsermanagerApiUrl($display_error = FALSE) {
    $usermanager_url = $this->configFactory->get('hms.settings')->get('user_manager_url');
    if (!empty($usermanager_url)) {
      return ($usermanager_url);
    }
    // Warn administrative user if the API URL is not set.
    elseif ($display_error) {
      $message = "For the premium functionality to work correctly, the Usermanager API URL needs to be set <a href='@url'>here</a>.";
      self::registerError($message, ['@url' => $GLOBALS['base_url'] . "/admin/config/people/hms"], 'warning');
    }
    return '';
  }

  /**
   * Gets the url for a certain resource.
   *
   * @param string $resource
   *
   * @return string
   *  Returns the resource URL string or an empty string.
   */
  public function getResourceUrl($resource) {
    switch($resource) {
      case 'price_category':
        return $this->getBestsellerApiUrl(TRUE) . self::PRICE_CATEGORY_API_PATH;
      case 'digtap_widgets':
        return $this->getBestsellerApiUrl(TRUE) . self::DIGTAP_WIDGET_JS_PATH;
      case 'premium_content':
        return $this->getUsermanagerApiUrl(TRUE) . self::PREMIUM_CONTENT_JS_PATH;
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
    $message = t($message, $substitutions);
    \Drupal::logger('hms_commerce')->notice($message);
    if (!empty($display)
      && \Drupal::currentUser()->hasPermission('administer hms_commerce settings')) {
      drupal_set_message($message, $display);
    }
  }
}
