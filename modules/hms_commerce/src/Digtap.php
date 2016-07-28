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
   * Saves a setting into Drupal configuration.
   *
   * @param string $setting_name
   *  The name of the setting.
   *
   * @param mixed $setting
   *  Absolute url string without trailing slash.
   */
  public function saveSetting($setting_name, $setting) {
    $this->configFactory->getEditable('hms_commerce.settings')
      ->set($setting_name, $setting)->save();
    // Refresh config object after making changes.
    $this->config = $this->configFactory->get('hms_commerce.settings');
  }

  /**
   * Gets a setting from Drupal configuration.
   *
   * @param string $setting_name
   *
   * @param string $error_message
   *  If setting is not set or empty, log/display an error message.
   *
   * @return mixed setting
   */
  public function getSetting($setting_name, $error_message = '') {
    switch($setting_name) {
      case 'usermanager_url':
        $setting = $this->configFactory->get('hms.settings')->get('user_manager_url');
        break;
      default:
        $setting = $this->config->get($setting_name);
    }
    if (empty($setting) && !empty($error_message)) {
      self::registerError($error_message, 'warning');
    }
    return $setting;
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
    $bestseller_url_error_message = t("For products to display, the Bestseller API URL needs to be set <a href='@url'>here</a>.", ['@url' => $GLOBALS['base_url'] . "/admin/config/hmscommerce"]);
    switch($resource) {

      case 'price_category':
        return $this->getSetting('bestseller_url', $bestseller_url_error_message) . self::PRICE_CATEGORY_API_PATH;

      case 'digtap_widgets':
        return $this->getSetting('bestseller_url', $bestseller_url_error_message) . self::DIGTAP_WIDGET_JS_PATH;

      case 'premium_content':
        $error_message = t("For the premium functionality to work correctly, the Usermanager API URL needs to be set <a href='@url'>here</a>.", ['@url' => $GLOBALS['base_url'] . "/admin/config/people/hms"]);
        return $this->getSetting('usermanager_url', $error_message) . self::PREMIUM_CONTENT_JS_PATH;
    }
    return '';
  }

  /**
   * Logs error and displays it to the privileged user.
   *
   * @param $message
   * @param string $display (optional)
   *  Message type (status/warning/error), if set, message is displayed to
   *  privileged user accordingly.
   */
  public static function registerError($message, $display = NULL) {
    \Drupal::logger('hms_commerce')->notice($message);
    if (!empty($display)
      && \Drupal::currentUser()->hasPermission('administer hms_commerce settings')) {
      drupal_set_message($message, $display);
    }
  }
}
