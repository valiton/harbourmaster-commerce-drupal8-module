<?php

namespace Drupal\hms_commerce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * SettingsFrom
 */
class SettingsForm extends ConfigFormBase {

  protected $settings;

  /**
   * SettingsForm constructor.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $settings
   */
  public function __construct($settings) {
    $this->settings = $settings;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('hms_commerce.settings')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'hms_commerce_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['hms_commerce.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['bestseller_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Bestseller API URL'),
      '#default_value' => $this->settings->getSetting('bestseller_url'),
      '#description' => $this->t('Absolute URL to the Bestseller API without trailing slash.'),
    ];

    $form['entitlement_group_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Entitlement group name'),
      '#default_value' => $this->settings->getSetting('entitlement_group_name'),
      '#description' => $this->t(''), //todo Add field description.
    ];

    $form['shared_secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shared secret key'),
      '#default_value' => $this->settings->getSetting('shared_secret_key'),
      '#description' => $this->t('Shared secret key used to encrypt the content.'),
    ];

    $form['premium_content_error'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Premium content error message'),
      '#default_value' => $this->settings->getSetting('premium_content_error'),
      '#description' => $this->t('A generic error that will be shown to end users when something goes wrong.'),
    ];

    $form['usermanager_configuration_link'] = [
      '#type' => 'markup',
      '#markup' => t("The usermanager API URL can be configured <a href='@url' target='_blank'>here</a>.", ['@url' => $GLOBALS['base_url'] . "/admin/config/people/harbourmaster#edit-usermanager-url"]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $url = $form_state->getValue('bestseller_url');
    if (!empty($url) && !UrlHelper::isValid($url, TRUE)) { // Check if URL looks valid.
      $form_state->setErrorByName(
        'bestseller_url', $this->t("<em>@path</em> is not a valid URL.", ['@path' => $url]));
    }
    elseif (substr($url, -1) == '/') { // Disallow trailing slash.
      $form_state->setErrorByName(
        'bestseller_url', $this->t("The URL may not contain a trailing slash.", ['@path' => $url]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->settings->saveSetting('bestseller_url', $form_state->getValue('bestseller_url'));
    $this->settings->saveSetting('entitlement_group_name', trim($form_state->getValue('entitlement_group_name')));
    $this->settings->saveSetting('shared_secret_key', trim($form_state->getValue('shared_secret_key')));
    $this->settings->saveSetting('premium_content_error', trim($form_state->getValue('premium_content_error')));
    parent::submitForm($form, $form_state);
  }
}
