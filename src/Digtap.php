<?php

namespace Drupal\hms_commerce;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\hms_commerce\Logger as HmsCommerceLogger;

/**
 * Digtap drupal service class.
 */
class Digtap {

  use StringTranslationTrait;

  private $configFactory;
  private $config;
  private $logger;
  private $currentUser;

  const PURCHASE_PRODUCT_TYPE_ID = 15;
  const ENTITLEMENT_PRODUCT_TYPE_ID = 17;
  const PRICE_CATEGORY_API_PATH = '/home/de/api/v1/products';
  const DIGTAP_WIDGET_FRONTEND_JS_PATH = '/bundles/digtapecom/widgets/frontend/stage/digtap-widget-frontend.min.js';
  const DIGTAP_WIDGET_BACKEND_JS_PATH = '/bundles/digtapecom/widgets/backend/stage/digtap-widget-backend.min.js';
  const PREMIUM_CONTENT_JS_PATH = '/usermanager/prod/js/premium-content.min.js';

  /**
   * Digtap constructor.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\hms_commerce\Logger $logger
   * @param $current_user
   */
  function __construct(
    ConfigFactoryInterface $config_factory,
    HmsCommerceLogger $logger,
    $current_user
  ) {
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
   * @return mixed setting
   */
  public function getSetting($setting_name) {
    switch($setting_name) {
      case 'usermanager_url':
        $setting = $this->configFactory->get('harbourmaster.settings')->get('user_manager_url');
        break;
      default:
        $setting = $this->config->get($setting_name);
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

    switch($resource) {
      case 'bestseller':
      case 'price_category':
      case 'digtap_frontend_widget':
      case 'digtap_backend_widget':

        $bestseller_url = $this->getSetting('bestseller_url');
        if (empty($bestseller_url)) {
          $this->logger
            ->m("For products to display, the Bestseller API URL needs to be set <a href='@url'>here</a>.", [
              '@url' => $GLOBALS['base_url'] . "/admin/config/hmscommerce"])
            ->display('warning', 'administer hms_commerce settings')
            ->log('warning');
        }

        switch($resource) {
          case 'bestseller':
            return $bestseller_url;

          case 'price_category':
            return $bestseller_url
            . self::PRICE_CATEGORY_API_PATH . '?'
            . http_build_query(['filter' => ['product_type_id' => [
              self::PURCHASE_PRODUCT_TYPE_ID,
              self::ENTITLEMENT_PRODUCT_TYPE_ID
            ]]]);

          case 'digtap_frontend_widget':
            return $bestseller_url . self::DIGTAP_WIDGET_FRONTEND_JS_PATH;

          case 'digtap_backend_widget':
            return $bestseller_url . self:: DIGTAP_WIDGET_BACKEND_JS_PATH;
        }
        break;

      case 'usermanager_url':
      case 'premium_content':

        $usermanager_url = $this->getSetting('usermanager_url');
        if (empty($usermanager_url)) {
          $this->logger
            ->m("For the premium functionality to work correctly, the Usermanager API URL needs to be set <a href='@url'>here</a>.", [
              '@url' => $GLOBALS['base_url'] . "/admin/config/people/harbourmaster"])
            ->display('warning', 'administer hms_commerce settings')
            ->log('warning');
        }

        switch($resource) {
          case 'usermanager_url':
            return $usermanager_url;
          case 'premium_content':
            return $usermanager_url . self::PREMIUM_CONTENT_JS_PATH;
        }
        break;
    }
    return '';
  }

  /**
   * @return array
   *  Array with category IDs as indexes and price categories as values.
   */
  public function getPriceCategories() {
    $categories = [];
    $url = $this->getResourceUrl('price_category');
    if (!empty($url)) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      if (($json = curl_exec($ch)) === FALSE) {
        $this->logger
          ->m("There was a problem connecting to the Bestseller API: Either the service is down, or an incorrect URL is set in the <a href='@url'>module settings</a>. The price category cannot be changed at this time.", ['@url' => $GLOBALS['base_url'] . "/admin/config/hmscommerce"])
          ->display('error', 'administer hms_commerce settings')
          ->m("cURL failed with error @code: @message", [
            '@code' => curl_errno($ch),
            '@message' => curl_error($ch)])
          ->log('error');
      }
      else {
        $response = json_decode($json);
        if (isset($response->_embedded->products)) {
          foreach($response->_embedded->products as $product) {
            $categories[$product->product_id] = $product->product;
          }
        }
        else {
          $this->logger
            ->m("The data the hms_commerce module received from Bestseller is not what it expected. This may indicate an outdated version of the Drupal hms_commerce module. The price category cannot be changed at this time.")
            ->display('error', 'administer hms_commerce settings')
            ->log('error');
        }
      }
      curl_close($ch);
    }
    return $categories;
  }
}
