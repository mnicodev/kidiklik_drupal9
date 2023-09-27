<?php

namespace Drupal\kidiklik_front\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a 'SortiesBoutonBlock' block.
 *
 * @Block(
 *  id = "sorties_bouton_block",
 *  admin_label = @Translation("Sorties bouton block"),
 * )
 */
class SortiesBoutonBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $build = [
      "#cache" => [
        "max-age" => 0,
        "contexts" => [],
        "tags" => [],
      ],
    ];

    $build['#theme'] = 'sortie_moment_bouton';
    $node = \Drupal::request()->get('node');

    
    

    if (!empty($node)) {
      if ($node->getType() === 'activite') {
        $build['#ref_act'] = $node->id();
        $seach_event = Views::getView("activites");
        $seach_event->setDisplay("search_agendas_activite");
        $seach_event->setArguments([$node->id()]);

        $seach_event->getQuery()->addWhere(1,'nid',$node->id(),'<>');
        $seach_event->execute();
        $events = \Drupal::service('renderer')->render($seach_event->render());
	
	$count_event = json_decode($events->__toString());
      
	if(count($count_event) === 0) {
		unset( $build['#ref_act']);
            $build['#ref_adh'] = current($node->get('field_adherent')->getValue())['target_id'];
            $seach_event = Views::getView("activites");
            $seach_event->setDisplay("search_agendas_adherent");
            $seach_event->setArguments([current($node->get('field_adherent')->getValue())['target_id']]);
          }

        $seach_event = Views::getView("activites");
        $seach_event->setDisplay("search_agendas_adherent");
        $seach_event->setArguments([current($node->get('field_adherent')->getValue())['target_id']]);

      } else {
        $build['#ref_adh'] = current($node->get('field_adherent')->getValue())['target_id'];
        $seach_event = Views::getView("activites");
        $seach_event->setDisplay("search_agendas_adherent");
        $seach_event->setArguments([$build['#ref_adh']]);
      }
      $seach_event->getQuery()->addWhere(1,'nid',$node->id(),'<>');
      $seach_event->execute();
      $events = \Drupal::service('renderer')->render($seach_event->render());
    }

    $count_event = [];
    if(!empty($events)) {
	    $count_event = json_decode($events->__toString());
    }
    
    if (empty($build['#ref_act']) || empty($build['#ref_adh'])) {
     // return null;
    }
    //return $build;
    if(count($count_event) > 0) {
      return $build;
    }
    
    
  }

}
