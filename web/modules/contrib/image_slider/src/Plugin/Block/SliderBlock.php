<?php

namespace Drupal\image_slider\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block.
 *
 * @Block(
 *   id = "slider_block",
 *   admin_label = @Translation("Slider Block"),
 *   deriver = "Drupal\image_slider\Plugin\Derivative\SliderBlock"
 * )
 */
class SliderBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The storage handler class for files.
   *
   * @var \Drupal\file\FileStorage
   */
  private $fileStorage;

  /**
   * Construct a MyFormatter object.
   *
   * @param array $configuration
   *   This is configuration.
   * @param string $plugin_id
   *   This is plugin.
   * @param mixed $plugin_definition
   *   This is plugin defination.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity
   *   Entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database, EntityTypeManagerInterface $entity) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->fileStorage = $entity->getStorage('file');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $blok_id = $this->getDerivativeId();
    $slider_data = [];
    $sql = $this->database->select('slider', 's')
      ->fields('s')
      ->condition('s.id', $blok_id, '=')
      ->execute()
      ->FetchAll();

    if (is_array($sql)&&count($sql) > 0) {

      $slider_data['id'] = $sql[0]->id;
      $slider_data['name'] = $sql[0]->name;
      $slider_data['slide_type'] = $sql[0]->slide_type;
      $slider_data['description'] = check_markup($sql[0]->description__value, 'full_html');
      $slider_data['langcode'] = $sql[0]->langcode;
      $image_sql = $this->database->select('image_slider__image', 's')
        ->fields('s')
        ->condition('s.entity_id', $slider_data['id'], '=')
        ->execute()
        ->FetchAll();
      if (is_array($image_sql)&&count($image_sql) > 0) {
        $i = 0;
        foreach ($image_sql as $val) {
          $slider_data['image'][$i]['image_alt'] = $val->image_alt;
          $file = $this->fileStorage->load($val->image_target_id);
          $image_url = Url::fromUri(file_create_url($file->getFileUri()))->toString();
          $slider_data['image'][$i]['image_url'] = $image_url;
          $i++;
        }
      }
    }

    $build = [
      '#theme' => 'image_slider',
      '#data' => $slider_data,
      '#attached' => [
        'library' => [
          'image_slider/image_slider_data',
        ],
        'drupalSettings' => [
          'image_slider' => [
            'slider_tyle' => $slider_data['slide_type'],
          ],
        ],
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

}
