<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;

/**
 * Provides a 'MiseEnAvantBlock' block.
 *
 * @Block(
 *  id = "mise_en_avant_block",
 *  admin_label = @Translation("Mise en avant block"),
 * )
 */
class MiseEnAvantBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
	public function build() {
		$view_entete = Views::getView("articles_content");
		$view_entete->setDisplay("bloc_mise_en_avant_dep");


		$view_entete->execute();

		$render_dep = $view_entete->render();

		$out = \Drupal::service('renderer')->render($view_entete->render());

		$view_nat = Views::getView("articles_content");
		$view_nat->setDisplay("bloc_mise_en_avant_nat");
		$view_nat->execute();
		$render_nat = $view_nat->render();
		$tab=[];

		foreach(current($render_dep['#rows'])['#rows'] as $key => $row) {

				$tab[] = $row;
				$tab[] = current($render_nat['#rows'])['#rows'][$key];

		}
		$render_dep['#rows'][0]['#rows'] = $tab;
		$out = \Drupal::service('renderer')->render($render_dep);
    $build = [];
    $build['#theme'] = 'mise_en_avant_block';
    $build['mise_en_avant_block']['#markup'] = $out;

    return $build;
  }

}
