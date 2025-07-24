<?php

namespace Drupal\otp_auth\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class OtpAccessSubscriber implements EventSubscriberInterface {

  protected $currentUser;
  protected $requestStack;

  public function __construct(AccountProxyInterface $currentUser, RequestStack $requestStack) {
    $this->currentUser = $currentUser;
    $this->requestStack = $requestStack;
  }

  public static function getSubscribedEvents() {
    return [
      KernelEvents::RESPONSE => ['checkOtpAccess', 0],
    ];
  }

  public function checkOtpAccess(ResponseEvent $event) {
    $request = $this->requestStack->getCurrentRequest();

    $current_path = $request->getPathInfo();
    //echo str_starts_with($current_path, '/otp-verify');exit;
    if (str_starts_with($current_path, '/otp-verify')) {
      return;
    }
    
    if (!$this->currentUser->isAuthenticated() || $this->currentUser->hasPermission('bypass otp verification')) {
      return;
    }
    
    $session = $request->getSession();
    if ($session->get('otp_verified')) {
      return;
    }

    // نفذ التحويل
    $url = \Drupal\Core\Url::fromRoute('otp_auth.verify')->toString();
    $response = new RedirectResponse($url);
    $event->setResponse($response);
  }
}
