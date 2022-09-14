<?php

namespace Drupal\kidiklik_base\Event\Listener;

use Drupal\Core\Site\Settings;
use Drupal\Core\Template\TwigEnvironment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *  * Class MyRequestListener
 *   *
 *    */
class RequestListener implements EventSubscriberInterface
{

  /**
   *    * @var \Drupal\Core\Template\TwigEnvironment
   *       */
  protected $twigEnvironment;

  /**
   *    * @var \Drupal\Core\Site\Settings
   *       */
  protected $settings;


  /**
   *    * FdeRequestListener constructor.
   *       */
  public function __construct(TwigEnvironment $twigEnvironment,
                              Settings        $settings)
  {

    $this->twigEnvironment = $twigEnvironment;
    $this->settings = $settings;
  }

  /**
   *    * @return mixed
   *       */
  public static function getSubscribedEvents()
  {
    $events[KernelEvents::REQUEST][] = ['onRequest'];
    return $events;
  }

  /**
   * @param GetResponseEvent $e
   */
  public function onRequest(GetResponseEvent $e)
  {
    $host = explode(".", \Drupal::request()->getHost());
    //$this->settings->addGlobal("dep",$host[0]);
    $this->twigEnvironment->addGlobal('dep', $host[0]);
  }
}

