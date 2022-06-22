<?php

namespace Drupal\kidiklik_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Class DefaultController.
 */
class DefaultController extends ControllerBase {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
  * Constructs a DefaultController object.
  *
  * @param \Drupal\Core\Database\Connection $database
  *   The database connection.
  */
  public function __construct(Connection $connection) {
    $this->connection=$connection;

  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Migrate.
   *
   * @return string
   *   Return Hello string.
   */
  public function migrateClient() {
    /*
    * on charge les contacts client

    $rs=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
      [
        "type"=>"contact",
        "field_type_contact"=>"client"
      ]
    );
    $save_client_id="";
    foreach($rs as $item) {

      $client_id=$item->get("field_ref")->value;
      $client=current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
          [
            "field_ref_client"=>$client_id,
            "type"=>"client"
          ]
        ));



      if(!empty($client)) {
        $client->get("field_contact")->appendItem($item);
        $client->validate();
        $client->save();

      }

    }
    */
    $rs=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
      [
        "type"=>"adherent",
      ]
    );

    $save_client_id="";
    foreach($rs as $item) {

      if($client_id=$item->get("field_ref_client")->value) {

        $client=current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
            [
              "field_ref_client"=>$client_id,
              "type"=>"client"
            ]
        ));



        if(!empty($client)) {
          $client->get("field_adherent")->appendItem($item);
          $client->validate();
          $client->save();

        }
      }


    }


    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: migrate with parameter(s): $content'),
    ];
  }

  public function migrateAdherent() {
    $rs=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
      [
        "type"=>"contact",
        "field_type_contact"=>"adherent"
      ]
    );
    $save_client_id="";
    foreach($rs as $item) {

      $client_id=$item->get("field_ref")->value;
      if($client_id!=$save_client_id) {
        $client=current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
          [
            "field_ref_client"=>$client_id,
            "type"=>"adherent"
          ]
        ));

        $save_client_id=$client_id;
      }

      if(!empty($client)) {
        $client->get("field_contact")->appendItem($item);
        $client->validate();
        $client->save();

      }

    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: migrate with parameter(s): $content'),
    ];
  }

}
