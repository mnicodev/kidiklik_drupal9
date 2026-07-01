<?php

/**
 * Extrait à copier dans le fichier <nom_du_theme>.theme de votre thème actif.
 *
 * Fournit la variable {{ event_schema_json }} utilisable dans
 * node--sorties-moment--full.html.twig.
 */

use Drupal\node\NodeInterface;

/**
 * Implements hook_preprocess_node().
 */
function MONTHEME_preprocess_node(array &$variables) {
  /** @var NodeInterface $node */
  $node = $variables['node'];

  if ($variables['view_mode'] !== 'full' || $node->bundle() !== 'sorties_moment') {
    return;
  }

  /** @var \Drupal\kidiklik_event_schema\EventSchemaBuilder $builder */
  $builder = \Drupal::service('kidiklik_event_schema.builder');
  $events = $builder->buildEventsForNode($node, TRUE, 20);

  if (empty($events)) {
    return;
  }

  $payload = count($events) === 1 ? $events[0] : $events;
  $variables['event_schema_json'] = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
