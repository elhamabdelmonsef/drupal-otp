<?php

namespace Drupal\otp_auth\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;

class OtpService {

  protected $config;
  protected $logger;
  protected $mail;

  public function __construct(ConfigFactoryInterface $config_factory, LoggerChannelFactoryInterface $logger_factory, MailManagerInterface $mail) {
    $this->config = $config_factory->get('otp_auth.settings');
    $this->logger = $logger_factory->get('otp_auth');
    $this->mail = $mail;
    //$this->mail->mail()
  }

  public function generateOtp($uid, $force = FALSE) {
    $user = User::load($uid);
    if (!$user) return;

    $otp = rand(pow(10, $this->config->get('otp_length') - 1), pow(10, $this->config->get('otp_length')) - 1);
    $store = &$_SESSION['otp_auth'][$uid];
    if ($force || empty($store['otp']) || time() > $store['expire']) {
      $store = [
        'otp' => $otp,
        'expire' => time() + $this->config->get('otp_expiration'),
      ];
      $this->logger->notice('OTP for user @uid: @otp', ['@uid' => $uid, '@otp' => $otp]);

      if (!empty($user->getEmail())) {
        // echo $user->getEmail();exit;
        $params['subject'] = 'Your OTP Code';
        $params['message'] = 'Your OTP is: ' . $otp;
        try {
          $this->mail->mail('otp_auth', 'otp', $user->getEmail(), 'en', $params);
        }
        catch (\Exception $e) {
          $this->logger->error('Email sending failed: @msg', ['@msg' => $e->getMessage()]);
        }
        
      }
    }
  }

  public function validateOtp($uid, $input_otp) {
    return isset($_SESSION['otp_auth'][$uid]) &&
           $_SESSION['otp_auth'][$uid]['otp'] == $input_otp && !$this->isOtpExpired($uid);
  }

  public function isOtpExpired($uid) {
    return !isset($_SESSION['otp_auth'][$uid]) || time() > $_SESSION['otp_auth'][$uid]['expire'];
  }
}
