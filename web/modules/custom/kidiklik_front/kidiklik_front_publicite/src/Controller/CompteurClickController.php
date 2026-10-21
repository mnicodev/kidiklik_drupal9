<?php

namespace Drupal\kidiklik_front_publicite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node;

/**
 * Class CompteurClickController.
 */
class CompteurClickController extends ControllerBase {

    /**
   * La connexion à la base de données.
   *
   * @var \Drupal\Core\Database\Connection
   */
    protected $database;    


    /**
   * {@inheritdoc}
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Addclick.
   *
   * @return string
   *   Return Hello string.
   */
  public function addClick($nid) {
      if(!empty($nid)) {
          // Incrément atomique directement en base, sans charger le node.
        $this->database->update('node__field_compteur_click')
        ->expression('field_compteur_click_value', 'field_compteur_click_value + 1')
        ->condition('entity_id', $nid)
        ->condition('deleted', 0)
        ->execute();

      // Relecture de la valeur mise à jour.
        $cpt = $this->database->select('node__field_compteur_click', 'c')
        ->fields('c', ['field_compteur_click_value'])
        ->condition('c.entity_id', $nid)
        ->condition('c.deleted', 0)
        ->execute()
        ->fetchField();

      // Invalide le cache du node pour rafraîchir l'affichage.
        Cache::invalidateTags(['node:' . $nid]);

        return new JsonResponse(['results' => $cpt]);
    }
    
    return new JsonResponse(['results' => null]);
  }

}
