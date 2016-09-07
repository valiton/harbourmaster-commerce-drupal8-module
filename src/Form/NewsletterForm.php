<?php

namespace Drupal\hms_commerce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class NewsletterForm
 * @package Drupal\hms_commerce\Form
 */
class NewsletterForm extends ConfigFormBase {

  protected $settings;

  const NUMBER_GROUPS = 5;

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
    return 'hms_commerce_newsletter_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['hms_commerce.settings'];
  }

  protected function getOrigin() {
    $site_name = \Drupal::config('system.site')->get('name');
    return 'DIGTAP_' . str_replace(' ', '_', $site_name);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['newsletter_client_id'] = [
      '#type' => 'number',
      '#title' => $this->t('Newsletter client ID'),
      '#default_value' => $this->settings->getSetting('client_id'),
      '#required' => TRUE,
      '#description' => $this->t('Client ID'),
      '#min' => 0,
    ];

    $form['newsletter_origin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Newsletter origin'),
      '#description' => $this->t('If left empty, <em>@origin</em> is used.', ['@origin' => $this->getOrigin()]),
      '#default_value' => $this->settings->getSetting('origin'),
    ];

    $form['newsletter_groups'] = [
      '#type' => 'details',
      '#title' => $this->t('Newsletter groups'),
      '#open' => FALSE,
      '#description' => $this->t('Newsletter groups'),
    ];

    $groups = $this->settings->getSetting('newsletter_groups');
    for ($i = 0; $i < self::NUMBER_GROUPS; $i++) {
      $form['newsletter_groups']['newsletter_group_' . $i] = [
        '#type' => 'textfield',
        '#title' => $this->t('Group @number', ['@number' => $i+1]),
        '#default_value' => isset($groups[$i]) ? $groups[$i] : '',
      ];
    }

    $form['show_contact_permission'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show contact permission checkbox'),
      '#default_value' => $this->settings->getSetting('show_contact_permission'),
    ];

    $form['show_privacy_permission'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show privacy permission checkbox'),
      '#default_value' => $this->settings->getSetting('show_privacy_permission'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }
}
