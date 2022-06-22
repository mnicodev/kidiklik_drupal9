<?php

namespace Drupal\image_slider\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for generating multiple block.
 *
 * @ingroup image_slider
 */
class SliderBlock extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Construct a MyFormatter object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // @todo might as well include your configuration here and assign a
    // value.
    $slider_data = [];
    $slider_data = $this->database->select('slider', 's')
      ->fields('s')
      ->execute()
      ->FetchAll();
    if (is_array($slider_data)&&count($slider_data) > 0) {
      foreach ($slider_data as $val) {
        $this->derivatives[$val->id] = $base_plugin_definition;
        $this->derivatives[$val->id]['admin_label'] = $val->name;
      }
    }
    else {
      $slider_data = ['0' => 'Slider Demo'];
      foreach ($slider_data as $key => $val) {
        $this->derivatives[$key] = $base_plugin_definition;
        $this->derivatives[$key]['admin_label'] = $val;
      }
    }

    return $this->derivatives;
  }

}
