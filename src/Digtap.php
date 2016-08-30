<?php

namespace Drupal\hms_commerce;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Digtap drupal service class.
 */
class Digtap {

  use StringTranslationTrait;

  private $configFactory;
  private $config;
  private $logger;
  private $currentUser;

  const PRICE_CATEGORY_API_PATH = '/home/de/api/v1/products';
  const DIGTAP_WIDGET_FRONTEND_JS_PATH = '/bundles/digtapecom/widgets/frontend/stage/digtap-widget-frontend.min.js';
  const DIGTAP_WIDGET_BACKEND_JS_PATH = '/bundles/digtapecom/widgets/backend/stage/digtap-widget-backend.min.js';
  const PREMIUM_CONTENT_JS_PATH = '/usermanager/prod/js/premium-content.min.js';

  /**
   * Digtap constructor.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param $logger
   * @param $current_user
   */
  function __construct(ConfigFactoryInterface $config_factory, $logger, $current_user) {
    $this->configFactory = $config_factory;
    $this->config = $config_factory->get('hms_commerce.settings');
    $this->logger = $logger;
    $this->currentUser = $current_user;
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
   * @param string|array $error_message
   *  If string or an array containing the message text and a
   *  placeholder => replacement array, log/display an error message.
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
      $this->registerError($error_message, 'warning');
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
    $bestseller_url = $this->getSetting('bestseller_url', ["For products to display, the Bestseller API URL needs to be set <a href='@url'>here</a>.", ['@url' => $GLOBALS['base_url'] . "/admin/config/hmscommerce"]]);
    switch($resource) {
      case 'bestseller':
        return $bestseller_url;

      case 'price_category':
        return $bestseller_url . self::PRICE_CATEGORY_API_PATH;

      case 'digtap_frontend_widget':
        return $bestseller_url . self::DIGTAP_WIDGET_FRONTEND_JS_PATH;

      case 'digtap_backend_widget':
        return $bestseller_url . self:: DIGTAP_WIDGET_BACKEND_JS_PATH;

      case 'premium_content':
        $error_message = ["For the premium functionality to work correctly, the Usermanager API URL needs to be set <a href='@url'>here</a>.", ['@url' => $GLOBALS['base_url'] . "/admin/config/people/harbourmaster"]];
        return $this->getSetting('usermanager_url', $error_message) . self::PREMIUM_CONTENT_JS_PATH;
    }
    return '';
  }

  /**
   * @return array
   *  Array with category IDs as indexes and price categories as values.
   *
   * @todo Either use curl or implement hook_requirements to check for allow_url_fopen.
   * @todo Adjust method to API which is to change.
   */
  public function getPriceCategories() {
    $categories = [];
    $url = $this->getResourceUrl('price_category');
    if (!empty($url)) {
      if (($json = @file_get_contents($url)) !== FALSE) {
        $response = json_decode($json);
        if (isset($response->_embedded->products)) {
          foreach($response->_embedded->products as $product) {
            $categories[$product->product_id] = $product->product;
          }
        }
        else {
          $this->registerError("The data the hms_commerce module received from Bestseller is not what it expected. This may indicate an outdated version of the Drupal hms_commerce module. The price category cannot be changed at this time.", 'error');
        }
      }
      else {
        $this->registerError(["There was a problem connecting to the Bestseller API: Either the service is down, or an incorrect URL is set in the <a href='@url'>module settings</a>. The price category cannot be changed at this time.", ['@url' => $GLOBALS['base_url'] . "/admin/config/hmscommerce"]], 'error');
      }
    }
    return $categories;
  }

  /**
   * Logs error and optionally displays it to the privileged user.
   *
   * @param $message
   *  Can be string or an array where the first value is the message string and
   *  the second value an array with arrays containing
   *  placeholder => replacement values for the message.
   * @param string $display
   *  Message type (status/warning/error), if set, message is displayed to
   *  privileged user in addition to being logged.
   */
  public function registerError($message, $display = NULL) {
    $substitutions = isset($message[1]) && is_array($message[1]) ? $message[1] : [];
    $message = is_array($message) ? $message[0] : $message;
    $this->logger->notice(strtr($message, $substitutions));
    if (!empty($display)
      && $this->currentUser->hasPermission('administer hms_commerce settings')) {
      drupal_set_message($this->t($message, $substitutions), $display);
    }
  }
}
