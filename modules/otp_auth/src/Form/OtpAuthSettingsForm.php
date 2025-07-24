<?php

namespace Drupal\otp_auth\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class OtpAuthSettingsForm extends ConfigFormBase {
  protected function getEditableConfigNames() {
    return ['otp_auth.settings'];
  }

  public function getFormId() {
    return 'otp_auth_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('otp_auth.settings');

    $form['otp_length'] = [
      '#type' => 'number',
      '#title' => $this->t('OTP Length'),
      '#default_value' => $config->get('otp_length') ?? 6,
      '#min' => 4,
      '#max' => 10,
      '#required' => TRUE,
    ];

    $form['otp_expiration'] = [
      '#type' => 'number',
      '#title' => $this->t('OTP Expiration (seconds)'),
      '#default_value' => $config->get('otp_expiration') ?? 300,
      '#min' => 60,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('otp_auth.settings')
      ->set('otp_length', $form_state->getValue('otp_length'))
      ->set('otp_expiration', $form_state->getValue('otp_expiration'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
