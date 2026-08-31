<?php

namespace Drupal\kidiklik_event_schema;

use Drupal\node\NodeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\file\Entity\File;

/**
 * Construit le tableau de données Event (schema.org) pour un nœud "sortie".
 *
 * IMPORTANT : adaptez les noms de champs (field_xxx) ci-dessous à ceux
 * réellement utilisés sur votre content type. Les noms utilisés ici sont
 * des exemples cohérents avec les fiches vues sur kidiklik.fr :
 *
 * - field_dates          : champ "daterange" à cardinalité illimitée
 *                          (une paire start/end par occurrence).
 * - field_horaire        : texte libre optionnel (ex: "à 11h, 14h et 16h",
 *                          "en soirée") utilisé uniquement pour la
 *                          description, pas pour construire une heure ISO.
 * - field_lieu_nom       : texte, nom du lieu (ex: "Château de Sully-sur-Loire").
 * - field_adresse        : champ "address" (module Address) ou des champs
 *                          simples adresse/CP/ville.
 * - field_geolocalisation: champ "geolocation" (lat/lng) ou deux champs
 *                          number simples.
 * - field_tarifs         : champ multivalué (paragraphes ou texte) avec
 *                          nom du tarif + prix.
 * - field_image          : champ image.
 * - field_organisateur   : texte ou référence à une entité "Organisateur".
 * - field_telephone      : texte.
 * - field_site_web       : lien externe.
 */
class EventSchemaBuilder {

  protected $entityTypeManager;
  protected $requestStack;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack) {
    $this->entityTypeManager = $entity_type_manager;
    $this->requestStack = $request_stack;
  }

  /**
   * Construit le tableau PHP représentant un ou plusieurs objets Event.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Le nœud "sortie".
   * @param bool $future_only
   *   Si TRUE (recommandé), ne génère que les occurrences dont la date de
   *   fin est >= aujourd'hui, pour ne jamais publier d'événements passés.
   * @param int|null $limit
   *   Nombre maximum d'occurrences à générer (utile si le champ de dates
   *   contient énormément d'entrées, ex: tous les jours d'un été).
   *   NULL = pas de limite.
   *
   * @return array
   *   Un tableau de tableaux associatifs, chacun représentant un objet
   *   JSON-LD "Event" prêt à être encodé en JSON.
   */
  public function buildEventsForNode(NodeInterface $node, bool $future_only = TRUE, ?int $limit = 20): array {
    $occurrences = $this->extractOccurrences($node, $future_only, $limit);

    if (empty($occurrences)) {
      return [];
    }

    $shared = $this->buildSharedProperties($node);

    $events = [];
    foreach ($occurrences as $occurrence) {
      $event = $shared;
      $event['startDate'] = $occurrence['start'];
     if ($occurrence['end'] !== null && $occurrence['end'] !== $occurrence['start']) {
        $event['endDate'] = $occurrence['end'];
      }

      $events[] = $event;
    }
    //echo "<pre>";var_dump($events);exit;

    return $events;
  }

  /**
   * Extrait les dates de début/fin depuis le champ daterange multivalué.
   *
   * Adapte automatiquement le format ISO selon que l'heure est connue
   * ou non (conformément aux recommandations Google : ne jamais mettre
   * une heure à 00:00:00 par défaut si elle n'est pas réellement connue).
   */
  protected function extractOccurrences(NodeInterface $node, bool $future_only, ?int $limit): array {
    if (!$node->hasField('field_date') || $node->get('field_date')->isEmpty()) {
      return [];
    }
    $now = new \DateTime('now', $timezone);

    $build_occurrence = function (\DateTime $base_start, \DateTime $base_end, bool $is_multi_day, ?array $time) use ($now, $future_only) {
      $start = clone $base_start;
      $end = clone $base_end;

      if ($time !== null) {
        [$h, $m] = $time;
        $start->setTime($h, $m, 0);
        if ($is_multi_day) {
          $end->setTime($h, $m, 0);
        }
      }

      if ($future_only) {
          $reference = $time !== null ? $start : $end;
        if ($reference < $now) {
          //return null;
        }
      }

      $has_time = ($time !== null);

      return [
        'start' => $has_time ? $start->format(\DateTime::ATOM) : $start->format('Y-m-d'),
        'end' => $is_multi_day
          ? ($has_time ? $end->format(\DateTime::ATOM) : $end->format('Y-m-d'))
          : null,
      ];
    };

    $horaire_raw = $node->hasField('field_horaires') && !$node->get('field_horaires')->isEmpty()
      ? $node->get('field_horaires')->value
      : '';
    $horaire_info = $this->parseHoraireField($horaire_raw);
    $timezone = new \DateTimeZone(date_default_timezone_get());
    $occurrences = [];
    $dates = $node->get('field_date')->referencedEntities();
    foreach ($dates as $paragraph) {
        $start_date = $paragraph->get('field_date_de_debut')->value;
        $end_date = $paragraph->get('field_date_de_fin')->value;

      if (empty($start_date) && empty($end_date)) {
        continue;
      }

      // Le champ daterange stocke les dates en UTC sans timezone ;
      // on les réinterprète dans le fuseau du site.
      $start = new \DateTime($start_date, new \DateTimeZone('UTC'));
      $start->setTimezone($timezone);

      $end = null;
      $end = new \DateTime($end_date, new \DateTimeZone('UTC'));
      $end->setTimezone($timezone);
      
      /*$is_multi_day = ($start->format('Y-m-d') !== $end->format('Y-m-d'));
      $date_key = $start->format('Y-m-d');
      if ($horaire_info['mode'] === 'global_multi') {

        // Plusieurs créneaux quotidiens (ex: "à 11h, 14h et 16h") :
        // on les applique TOUS à cette date -> produit croisé.
        foreach ($horaire_info['times'] as $time) {
          $occ = $build_occurrence($start, $end, $is_multi_day, $time);
          if ($occ !== null) {
            $occurrences[] = $occ;
          }
        }
        continue;
      }

     
      // Détermine l'heure à appliquer à CETTE occurrence précise,
      // selon ce que parseHoraireField() a pu déduire.
      $time = null;
      if ($horaire_info['mode'] === 'per_date' && isset($horaire_info['map'][$date_key])) {
        $time = $horaire_info['map'][$date_key];
      }
      elseif ($horaire_info['mode'] === 'global') {
        $time = $horaire_info['time'];
      }
      // mode 'none' ou date absente de la map -> $time reste null,
      // on ne met pas d'heure plutôt que d'en inventer une fausse.
 
      if ($time !== null) {
        [$h, $m] = $time;
        $start->setTime($h, $m, 0);
        $end->setTime($h, $m, 0);
      }*/
 
      if ($future_only) {
        $reference = $time !== null ? $start : $end;
        if ($reference < $now) {
          // On ignore les occurrences déjà passées. Quand on n'a pas
          // d'heure, on compare sur la date de fin pour éviter d'exclure
          // à tort un événement du jour même avant qu'il ait eu lieu.
          continue;
        }
      }
 
      $has_time = ($time !== null);


      $occurrences[] = [
        'start' => $has_time ? $start->format(\DateTime::ATOM) : $start->format('Y-m-d'),
        'end' => $has_time ? $end->format(\DateTime::ATOM) : $end->format('Y-m-d'),
      ];
    }

    // Tri chronologique.
    usort($occurrences, function ($a, $b) {
      return strcmp($a['start'], $b['start']);
    });

    if ($limit !== null && count($occurrences) > $limit) {
      $occurrences = array_slice($occurrences, 0, $limit);
    }

    return $occurrences;
  }

  protected function parseHoraireField(string $raw): array {
    $raw = trim($raw);
    if ($raw === '') {
      return ['mode' => 'none'];
    }

    $mois = [
      'janvier' => 1, 'février' => 2, 'fevrier' => 2, 'mars' => 3,
      'avril' => 4, 'mai' => 5, 'juin' => 6, 'juillet' => 7,
      'août' => 8, 'aout' => 8, 'septembre' => 9, 'octobre' => 10,
      'novembre' => 11, 'décembre' => 12, 'decembre' => 12,
    ];
    $mois_pattern = implode('|', array_keys($mois));

    // Cas 2 : lignes "jour_semaine JJ(er) mois AAAA à HHhMM".
    // On cherche toutes les occurrences de ce pattern dans le texte,
    // qu'elles soient sur des lignes séparées ou non.
    $date_time_pattern =
      '/(\d{1,2})(?:er)?\s+(' . $mois_pattern . ')\s+(\d{4})\s+(?:à|a)\s+(\d{1,2})h(\d{2})?/iu';

    if (preg_match_all($date_time_pattern, $raw, $matches, PREG_SET_ORDER)) {
      $map = [];
      foreach ($matches as $match) {
        $day = (int) $match[1];
        $month = $mois[mb_strtolower($match[2])];
        $year = (int) $match[3];
        $hour = (int) $match[4];
        $minute = isset($match[5]) && $match[5] !== '' ? (int) $match[5] : 0;

        $key = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $map[$key] = [$hour, $minute];
      }
      if (!empty($map)) {
        return ['mode' => 'per_date', 'map' => $map];
      }
    }

    // Cas 1 : une ou plusieurs heures isolées du type "à 11h" ou
    // "à 11h, 14h et 16h", sans date associée dans le texte.
    // - Une seule heure trouvée -> s'applique à toutes les occurrences.
    // - Plusieurs heures trouvées -> on considère qu'elles s'appliquent
    //   TOUTES à CHAQUE date de field_dates (cas des créneaux
    //   quotidiens répétés, ex: "visites à 11h, 14h et 16h" tous les
    //   jours de la période). On génère alors un produit croisé
    //   date × heure dans extractOccurrences().
    $any_time_pattern = '/\b(\d{1,2})h(\d{2})?\b/iu';
    if (preg_match_all($any_time_pattern, $raw, $matches, PREG_SET_ORDER)) {
      $times = [];
      foreach ($matches as $match) {
        $hour = (int) $match[1];
        $minute = isset($match[2]) && $match[2] !== '' ? (int) $match[2] : 0;
        $times[] = [$hour, $minute];
      }
      if (count($times) === 1) {
        return ['mode' => 'global', 'time' => $times[0]];
      }
      return ['mode' => 'global_multi', 'times' => $times];
    }

    // Rien d'exploitable : "en soirée", "l'après-midi", texte vide, etc.
    return ['mode' => 'none'];
  }



  /**
   * Construit les propriétés communes à toutes les occurrences
   * (tout sauf startDate/endDate, qui varient par occurrence).
   */
  protected function buildSharedProperties(NodeInterface $node): array {
    $request = $this->requestStack->getCurrentRequest();
    $base_url = $request ? $request->getSchemeAndHttpHost() : '';
    $node_url = $node->toUrl('canonical', ['absolute' => TRUE])->toString();

    $event = [
      '@context' => 'https://schema.org',
      '@type' => 'Event',
      'name' => $node->getTitle(),
      'identifier' => $node->id(),
      'eventStatus' => 'https://schema.org/EventScheduled',
      'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
    ];

    // Description : on privilégie le champ meta-description / résumé
    // du body plutôt que le corps complet (trop long, non pertinent ici).
    if ($node->hasField('field_resume') && !$node->get('field_resume')->isEmpty()) {
      $event['description'] = $node->get('field_resume')->value;
    }
    elseif ($node->hasField('body') && !$node->get('body')->isEmpty()) {
      $summary = $node->get('body')->summary;
      $event['description'] = $summary ?: trim(strip_tags(substr($node->get('body')->value, 0, 300)));
    }

    // Lieu.
    $event['location'] = $this->buildLocation($node);

    // Image.
    if ($node->hasField('field_image') && !$node->get('field_image')->isEmpty()) {
      $file = $node->get('field_image')->entity;
      if ($file) {
            $event['image'] = file_create_url($file->getFileUri());
        /*try {
            $event['image'] = \Drupal::service('file_url_generator')
                ->generateAbsoluteString($file->getFileUri());
        } catch(Exception $exception) {
            $event['image'] = file_create_url($file->getFileUri());
        }*/
      } else {
          $event['image'] = 'https://www.kidiklik.fr/assets/img/piaf.jpg';
      }
    }

    // Tarifs / offers.
    $offers = $this->buildOffers($node, $node_url);
    if (!empty($offers)) {
      $event['offers'] = $offers;
    }

    // Organisateur.
    $organizer = $this->buildOrganizer($node, $node_url);
    if (!empty($organizer)) {
      $event['organizer'] = $organizer;
    }
    $event['audience'] = [
        '@type' => 'Audience',
        'audienceType' => 'Family',
    ];

    return $event;
  }

  /**
   * Construit le bloc "location" (Place + PostalAddress + GeoCoordinates).
   */
  protected function buildLocation(NodeInterface $node): array {
    $location = [
      '@type' => 'Place',
    ];

    if ($node->hasField('field_lieu') && !$node->get('field_lieu')->isEmpty()) {
      $location['name'] = $node->get('field_lieu')->value;
    }

    $address = ['@type' => 'PostalAddress'];
    if ($node->hasField('field_adresse') && !$node->get('field_adresse')->isEmpty()) {
      $address['streetAddress'] = $node->get('field_adresse')->value;
    }
    if ($node->hasField('field_code_postal') && !$node->get('field_code_postal')->isEmpty()) {
      $address['postalCode'] = $node->get('field_code_postal')->value;
    }
    if ($node->hasField('field_ville_save') && !$node->get('field_ville_save')->isEmpty()) {
      $address['addressLocality'] = $node->get('field_ville_save')->value;
    }
    $address['addressCountry'] = 'FR';
    $location['address'] = $address;
    if ($node->hasField('field_geolocation_demo_single') && !$node->get('field_geolocation_demo_single')->isEmpty()) {

      $geo = $node->get('field_geolocation_demo_single')->first()->getValue();
      if (!empty($geo['lat']) && !empty($geo['lng'])) {
        $location['geo'] = [
          '@type' => 'GeoCoordinates',
          'latitude' => (float) $geo['lat'],
          'longitude' => (float) $geo['lng'],
        ];
      }
    }

    return $location;
  }

  /**
   * Construit le(s) objet(s) "Offer".
   *
   * Adaptez à la structure réelle de votre champ tarifs (paragraphe
   * répétable avec "nom" + "prix", ou simple champ texte/decimal).
   */
  protected function buildOffers(NodeInterface $node, string $node_url): array {
      // Cas simple : un seul champ prix décimal (0 = gratuit).
      if(!$node->hasField('field_prix')) {
        return [];
      }
      $offers = [];
      foreach ($node->get('field_prix') as $delta => $item) {
        $offers[] = [
          '@type' => 'Offer',
          'price' => (string) $item->value,
          'priceCurrency' => 'EUR',
          'availability' => 'https://schema.org/InStock',
          'url' => $node_url,
        ];
      }
      return count($offers) === 1 ? $offers[0] : $offers;

    return [];
  }

  /**
   * Construit le bloc "organizer".
   */
  protected function buildOrganizer(NodeInterface $node, $node_url): array {
    if (!$node->hasField('field_lieu') || $node->get('field_lieu')->isEmpty()) {
      return [];
    }

    $organizer = [
      '@type' => 'Organization',
      'name' => $node->get('field_lieu')->value,
      'url' => $node_url,
    ];


    return $organizer;
  }

}
