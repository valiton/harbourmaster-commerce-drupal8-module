<?php

namespace Drupal\hms_commerce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hms_commerce\Digtap;

/**
 * Class NewsletterForm
 * @package Drupal\hms_commerce\Form
 */
class NewsletterForm extends ConfigFormBase {

  protected $settings;

  /**
   * SettingsForm constructor.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $settings
   */
  public function __construct(Digtap $settings) {
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

  public static function getOrigin() {
    $site_name = \Drupal::config('system.site')->get('name');
    return 'DIGTAP_' . str_replace(' ', '_', $site_name);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;

    $form['newsletter_client_id'] = [
      '#type' => 'number',
      '#title' => $this->t('Newsletter client ID'),
      '#default_value' => $this->settings->getSetting('newsletter_client_id'),
      '#required' => TRUE,
      '#description' => $this->t('The unique identifying client ID.'),
      '#min' => 0,
    ];

    $form['newsletter_origin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Newsletter subscription origin'),
      '#description' => $this->t('Enter the name which will be used to define where a user subscribes from. If left empty, <em>@origin</em> is used.', ['@origin' => $this::getOrigin()]),
      '#default_value' => $this->settings->getSetting('newsletter_origin'),
    ];

    $form['newsletter_groups'] = [
      '#type' => 'details',
      '#title' => $this->t('Newsletter groups'),
      '#prefix' => '<div id="newsletter-groups-fieldset-wrapper">',
      '#suffix' => '</div>',
      '#description' => $this->t('Users will be able to subscribe to newsletters defined here.'),
      '#open' => TRUE,
    ];

    if ($initial = is_null($form_state->get('newsletter_groups_num'))) {
      $newsletter_groups = $this->settings->getSetting('newsletter_groups');
      $newsletter_groups_num = count($newsletter_groups);
      $form_state->set('newsletter_groups_num', $newsletter_groups_num);
    }
    else {
      $newsletter_groups_num = $form_state->get('newsletter_groups_num');
    }

    for ($i = 0; $i < $newsletter_groups_num; $i++) {

      $form['newsletter_groups'][$i] = [
        '#attributes' => array('class' => array('container-inline')),
        '#type' => 'fieldset',
      ];

      $form['newsletter_groups'][$i]['id'] = [
        '#type' => 'number',
        '#title' => t('Id'),
        '#min' => 0,
      ];

      $form['newsletter_groups'][$i]['name'] = [
        '#type' => 'textfield',
        '#title' => t('Text'),
      ];

      if ($initial) {
        $form['newsletter_groups'][$i]['id']['#default_value'] = $newsletter_groups[$i]['id'];
        $form['newsletter_groups'][$i]['name']['#default_value'] = $newsletter_groups[$i]['name'];
      }
    }

    $form['newsletter_groups']['actions']['add_group'] = [
      '#type' => 'submit',
      '#value' => t('Add one more'),
      '#submit' => array('::addOne'),
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'newsletter-groups-fieldset-wrapper',
      ],

      '#validate' => [],
    ];
/*    if ($newsletter_groups_num > 1) {
      $form['newsletter_groups']['actions']['remove_group'] = [
        '#type' => 'submit',
        '#value' => t('Remove one'),
        '#submit' => array('::removeCallback'),
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'newsletter-groups-fieldset-wrapper',
        ],
        '#validate' => [],
      ];
    }*/
    $form_state->setCached(FALSE);

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

  public function addOne(array &$form, FormStateInterface $form_state) {
    $form_state->set('newsletter_groups_num', $form_state->get('newsletter_groups_num') + 1);
    $form_state->setRebuild();
  }

  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['newsletter_groups'];
  }

/*  public function removeCallback(array &$form, FormStateInterface $form_state) {
    if (($newsletter_groups_num = $form_state->get('newsletter_groups_num')) > 1) {
      $form_state->set('newsletter_groups_num', $newsletter_groups_num - 1);
    }
    $form_state->setRebuild();
  }*/


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $groups = $form_state->getValue('newsletter_groups');

    // Require the ID if name given.
    foreach ($groups as $i => $group_data) {
      if (!empty($group_data['name']) && empty($group_data['id'])) {
        $form_state->setErrorByName(
          'newsletter_groups', $this->t("Every newsletter group requires an ID."));
      }
    }

    // Make sure the IDs are unique.
    $group_num = 0;
    $unique_keys = [];
    foreach ($groups as $i => $group_data) {
      if (!empty($group_data['id'])) {
        $group_num++;
        $unique_keys[$group_data['id']] = $group_data['id'];
      }
    }
    if ($group_num > count($unique_keys)) {
      $form_state->setErrorByName(
        'newsletter_groups', $this->t("Newsletter IDs must be unique for each group."));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $groups = $form_state->getValue('newsletter_groups');
    foreach ($groups as $i => &$group_data) {
      if (empty($group_data['name'])) {
        unset($groups[$i]);
        continue;
      }
      $group_data['name'] = trim($group_data['name']);
    }
    $this->settings->saveSetting('newsletter_groups', array_values($groups));
    $this->settings->saveSetting('newsletter_client_id', $form_state->getValue('newsletter_client_id'));
    $this->settings->saveSetting('newsletter_origin', trim($form_state->getValue('newsletter_origin')));
    $this->settings->saveSetting('show_contact_permission', $form_state->getValue('show_contact_permission'));
    $this->settings->saveSetting('show_privacy_permission', $form_state->getValue('show_privacy_permission'));

    parent::submitForm($form, $form_state);
  }
}
