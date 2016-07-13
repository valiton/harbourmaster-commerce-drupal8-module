<?php

namespace Drupal\hms_commerce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * SettingsFrom
 */
class SettingsForm extends ConfigFormBase {

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
    $form['api_source'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Commerce API URL'),
      '#default_value' => $this->config('hms_commerce.settings')->get('api_source'),
      '#description' => $this->t('Absolute URL to the commerce API.'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!UrlHelper::isValid($form_state->getValue('api_source'), TRUE)) {
      $form_state->setErrorByName('api_source', t("<em>@path</em> is not a valid URL.", ['@path' => $form_state->getValue('api_source')]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('hms_commerce.settings');
    $config->set('api_source', $form_state->getValue('api_source'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
