<?php

namespace Drupal\imageapi_optimize_tinypng\Plugin\ImageAPIOptimizeProcessor;

use Tinify\Exception as TinifyException;
use Tinify\ConnectionException;
use Tinify\ServerException;
use Tinify\ClientException;
use Tinify\AccountException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageFactory;
use Drupal\imageapi_optimize\ConfigurableImageAPIOptimizeProcessorBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Uses the TinyPNG webservice to optimize an image.
 *
 * @ImageAPIOptimizeProcessor(
 *   id = "tinypng",
 *   label = @Translation("TinyPNG"),
 *   description = @Translation("Uses the TinyPNG service to optimize jpeg and png images.")
 * )
 */
class TinyPng extends ConfigurableImageAPIOptimizeProcessorBase {

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerInterface $logger, ImageFactory $image_factory, FileSystemInterface $file_system) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger, $image_factory);

    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('imageapi_optimize'),
      $container->get('image.factory'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function applyToImage($image_uri) {
    $apiKey = $this->configuration['api_key'];
    try {
      \Tinify\setKey($apiKey);
      $sourceImage = file_get_contents($image_uri);
      if ($optimizedImage = \Tinify\fromBuffer($sourceImage)->toBuffer()) {
        if ($this->fileSystem->saveData($optimizedImage, $image_uri, FileSystemInterface::EXISTS_REPLACE)) {
          return TRUE;
        }
      }
    }
    catch (AccountException $e) {
      // Verify your API key and account limit.
      $this->logger->error('TinyPNG - AccountException: Failed to download optimize image using TinyPNG due to "%error".', ['%error' => $e->getMessage()]);
    }
    catch (ClientException $e) {
      // Check your source image and request options.
      $this->logger->error('TinyPNG - ClientException: Failed to download optimize image using TinyPNG due to "%error".', ['%error' => $e->getMessage()]);
    }
    catch (ServerException $e) {
      // Temporary issue with the Tinify API.
      $this->logger->error('TinyPNG - ConnectionException: Failed to download optimize image using TinyPNG due to "%error".', ['%error' => $e->getMessage()]);
    }
    catch (ConnectionException $e) {
      // A network connection error occurred.
      $this->logger->error('TinyPNG - ConnectionException: Failed to download optimize image using TinyPNG due to "%error".', ['%error' => $e->getMessage()]);
    }
    catch (\Exception $e) {
      // Something else went wrong, unrelated to the Tinify API.
      $this->logger->error('TinyPNG: Failed to download optimize image using TinyPNG due to "%error".', ['%error' => $e->getMessage()]);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'api_key' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('TinyPNG API key'),
      '#description' => $this->t('Enter required TinyPNG API key. Get your API key from <a href="https://tinypng.com" target="_blank">https://tinypng.com</a>'),
      '#default_value' => $this->configuration['api_key'],
      '#size' => 32,
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);

    try {
      \Tinify\setKey($form_state->getValue('api_key'));
      \Tinify\validate();
    }
    catch (TinifyException $e) {
      // Validation of API key failed.
      $form_state->setError($form['api_key'], $this->t('Unable to validate as a TinyPNG API key. TinyPNG error message was: %message', ['%message' => $e->getMessage()]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['api_key'] = $form_state->getValue('api_key');
  }

}
