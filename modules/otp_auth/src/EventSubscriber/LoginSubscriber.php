<?php

namespace Drupal\otp_auth\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Drupal\otp_auth\Service\OtpService;


class LoginSubscriber implements EventSubscriberInterface {

    protected $currentUser;
    protected $requestStack;
    protected $otpService;


    public function __construct(AccountProxyInterface $current_user, RequestStack $request_stack, OtpService $otp_service) {
        $this->currentUser = $current_user;
        $this->requestStack = $request_stack;
        $this->otpService = $otp_service;
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

  public function onKernelResponse(ResponseEvent $event) {
    $request = $this->requestStack->getCurrentRequest();
    $session = $request->getSession();

    // Only do this once after login
    if (
        $this->currentUser->isAuthenticated()
        && !$this->currentUser->hasPermission('bypass otp verification')
        && !$session->get('otp_verified')
        && !$session->get('otp_redirect_handled')
      ) {
        // Set session flags and generate OTP
        $this->otpService->generateOtp($this->currentUser->id());
        $session->set('otp_verified', FALSE);
        $session->set('otp_target_url', $request->query->get('destination') ?? '/');
        $session->set('otp_redirect_handled', TRUE); // prevent redirect loop
  
        // Prevent redirect on the OTP page itself
        if (!str_starts_with($request->getPathInfo(), '/otp-verify')) {
            $url = \Drupal\Core\Url::fromRoute('otp_auth.verify')->toString();
            $response = new RedirectResponse($url);
            $event->setResponse($response);
        }
      } 
  }
}