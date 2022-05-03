<?php

namespace Drupal\d8_activecampaign\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Administration form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'd8_activecampaign_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'd8_activecampaign.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $field_type = NULL) {
    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config('d8_activecampaign.settings');

    $this->apiSettings($form, $form_state, $config);
    $this->listMappingSettings($form, $form_state, $config);

    return parent::buildForm($form, $form_state);
  }

  /**
   * AC API Specific settings, i.e. API URL & Key.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param \Drupal\Core\Config\Config $config
   *
   * @return void
   */
  public function apiSettings(array &$form, FormStateInterface &$form_state, Config $config) {
    $form['d8_activecampaign'] = [
      '#type' => 'details',
      '#title' => $this->t('ActiveCampaign API Settings'),
      '#open' => TRUE,
    ];

    $form['d8_activecampaign']['api_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API URL'),
      '#default_value' => $config->get('api_url'),
      '#description' => $this->t('Set this to the API URL that ActiveCampaign has provided for you.'),
      '#required' => TRUE,
    ];

    $form['d8_activecampaign']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $config->get('api_key'),
      '#description' => $this->t('Set this to the API Key that ActiveCampaign has provided for you.'),
      '#required' => TRUE,
    ];
  }

  /**
   * AC List mappings.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param \Drupal\Core\Config\Config $config
   *
   * @return void
   */
  public function listMappingSettings(array &$form, FormStateInterface &$form_state, Config $config) {
    $form['d8_activecampaign']['api_lists'] = [
      '#type' => 'details',
      '#title' => $this->t('ActiveCampaign List Mappings'),
      '#open' => TRUE,
    ];

    $form['d8_activecampaign']['api_lists']['all_user_list'] = [
      '#type' => 'textfield',
      '#title' => $this->t('AC List ID for all user accounts'),
      '#default_value' => $config->get('all_user_list'),
      '#description' => $this->t('This is the ActiveCampaign list that all users are added to.'),
      '#required' => TRUE,
    ];

    $form['d8_activecampaign']['api_lists']['newsletter_user_list'] = [
      '#type' => 'textfield',
      '#title' => $this->t('AC List ID for newsletter subscribed user accounts'),
      '#default_value' => $config->get('newsletter_user_list'),
      '#description' => $this->t('This is the ActiveCampaign list for users that subscribe to the newsletter.'),
      '#required' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // @todo: Check values with API.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config('d8_activecampaign.settings');
    $keys = ['api_key', 'api_url', 'all_user_list', 'newsletter_user_list'];

    foreach ($keys as $key) {
      $config->set($key, $form_state->getValue($key));
    }

    $config->save();
  }

}
