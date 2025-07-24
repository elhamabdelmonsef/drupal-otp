<?php

namespace Drupal\otp_auth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBuilderInterface;

/**
 * Controller to render the OTP form inside a themed page.
 */
class OtpVerificationController extends ControllerBase {

  protected $formBuilder;

  public function __construct(FormBuilderInterface $formBuilder) {
    $this->formBuilder = $formBuilder;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  public function verifyPage() {
    return [
      '#title' => $this->t('OTP Verification'),
      'form' => $this->formBuilder->getForm('Drupal\otp_auth\Form\OtpVerificationForm'),
    ];

  }

}
