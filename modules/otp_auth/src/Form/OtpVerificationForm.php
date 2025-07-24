<?php

namespace Drupal\otp_auth\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\otp_auth\Service\OtpService;
use Drupal\Core\Session\AccountProxyInterface;

class OtpVerificationForm extends FormBase {

    protected $otpService;
    protected $currentUser;

    public function __construct(OtpService $otpService, AccountProxyInterface $current_user) {
        $this->otpService = $otpService;
        $this->currentUser = $current_user;
    }

    public static function create(ContainerInterface $container) {
        return new static(
        $container->get('otp_auth.otp_service'),
        $container->get('current_user')
        );
    }

    public function getFormId() {
        return 'otp_verification_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['otp'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Enter the OTP'),
        '#required' => TRUE,
        '#attributes' => [
            'pattern' => '[0-9]*',
            'inputmode' => 'numeric',
            'title' => $this->t('Please enter numbers only.'),
            'maxlength' => 6,
        ],
        ];

        $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Verify'),
        ];

        $form['actions']['resend'] = [
            '#type' => 'submit',
            '#value' => $this->t('Resend OTP'),
            '#submit' => ['::resendOtp'],
            '#limit_validation_errors' => [],
        ];

        //return parent::buildForm($form, $form_state);
        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $session = \Drupal::request()->getSession();
        $otp = $form_state->getValue('otp');
        $uid = $this->currentUser->id();

        if ($this->otpService->validateOtp($uid, $otp)) {
            $session->set('otp_verified', TRUE);
            $form_state->setRedirect('<front>');
        }
        else {
        $this->messenger()->addError($this->t('Invalid OTP. Please try again.'));
        }
    }

    public function resendOtp(array &$form, FormStateInterface $form_state) {
        $uid = $this->currentUser()->id();
    
        $this->otpService->generateOtp($uid, TRUE);
    
        $this->messenger()->addMessage($this->t('A new OTP has been sent.'));
        $form_state->setRebuild(TRUE);
    }
  

    public function validateForm(array &$form, FormStateInterface $form_state) {
        $otp = $form_state->getValue('otp');
    
        if (!ctype_digit($otp)) {
        $form_state->setErrorByName('otp', $this->t('OTP must contain digits only.'));
        }
    
        parent::validateForm($form, $form_state);
    }
}
