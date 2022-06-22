<?php

namespace Drupal\Tests\simple_fb_connect\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\simple_fb_connect\SimpleFbConnectFbManager;

/**
 * @coversDefaultClass Drupal\simple_fb_connect\SimpleFbConnectFbManager
 * @group simple_fb_connect
 */
class SimpleFbConnectFbManagerTest extends UnitTestCase {

  /**
   * Used for logging errors.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Used for dispatching events to other modules.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Used for access Drupal user field definitions.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Used for reading data from and writing data to session.
   *
   * @var \Drupal\simple_fb_connect\SimpleFbConnectPersistentDataHandler
   */
  protected $persistentDataHandler;

  /**
   * Facebook.
   *
   * @var \Facebook\facebook
   */
  protected $facebook;

  /**
   * Fb manager.
   *
   * @var mixed
   */
  protected $fbManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->loggerFactory = $this->createMock('Drupal\Core\Logger\LoggerChannelFactoryInterface');

    $this->eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

    $this->entityFieldManager = $this->createMock('Drupal\Core\Entity\EntityFieldManagerInterface');

    $this->urlGenerator = $this->createMock('Drupal\Core\Routing\UrlGeneratorInterface');

    $this->persistentDataHandler = $this->getMockBuilder('Drupal\simple_fb_connect\SimpleFbConnectPersistentDataHandler')
      ->disableOriginalConstructor()
      ->getMock();

    $this->fbManager = new SimpleFbConnectFbManager(
      $this->loggerFactory,
      $this->eventDispatcher,
      $this->entityFieldManager,
      $this->urlGenerator,
      $this->persistentDataHandler
    );

    $this->facebook = $this->getMockBuilder('Facebook\Facebook')
      ->disableOriginalConstructor()
      ->getMock();
    $this->fbManager->setFacebookService($this->facebook);
  }

  /**
   * Tests getFbReRequestUrl method.
   *
   * @covers ::getFbReRequestUrl
   */
  public function testGetFbReRequestUrl() {
    $login_helper = $this->getMockBuilder('Facebook\Helpers\FacebookRedirectLoginHelper')
      ->disableOriginalConstructor()
      ->getMock();

    $login_helper
      ->expects($this->once())
      ->method('getReRequestUrl')
      ->willReturn('https://www.facebook.com/rerequest-url');

    $this->facebook
      ->expects($this->once())
      ->method('getRedirectLoginHelper')
      ->willReturn($login_helper);

    $this->urlGenerator
      ->expects($this->once())
      ->method('generateFromRoute')
      ->willReturn('http://www.example.com/user/simple-fb-connect/return');

    $this->assertSame($this->fbManager->getFbReRequestUrl(), 'https://www.facebook.com/rerequest-url');
  }

  // TODO: write more tests for this class.
}
