<?php

namespace Drupal\kidiklik_migrate\Commands;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\User;
use Drupal\taxonomy\Entity\Term;
use Drush\Commands\DrushCommands;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\kidiklik_base\KidiklikEntity;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 */
class KidiklikCommands extends DrushCommands
{
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
  public function __construct(Connection $connection)
  {
    $this->connection = $connection;

  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('database')
    );
  }

  public function propre($str,$chariot=null) {
    $desc=str_replace('&eacute;','é',$str);
    $desc=str_replace('&egrave;','è',$desc);
    $desc=str_replace('&ecirc;;','ê',$desc);
    $desc=str_replace('&ocirc;','ô',$desc);
    $desc=str_replace('&acirc;','â',$desc);
    $desc=str_replace('NULL','',$desc);
    $desc=str_replace('&agrave;','à',$desc);
    $desc=str_replace('&amp;','&',$desc);
    //$desc=str_replace(['&lt;p&gt;','&lt;/p&gt;'],['<p>','</p>'],$desc);
    if($chariot===true) {
      //$desc=str_replace(['&#13','&#10;'],['<br>','<br>'],$desc);
    }
    
    
    $desc = str_replace(["&#39;","&#34;", '&#38;'], ["'",'"', '&'],htmlspecialchars_decode($desc));
    return $desc;
  }

  /**
   * Echos back hello with the argument provided.
   *
   * @param string $name
   *   Argument provided to the drush command.
   *
   * @command kidiklik_migrate:cmd
   * @aliases kdk
   * @options arr An option that takes multiple values.
   * @options msg Whether or not an extra message should be displayed to the user.
   * @usage drush9_example:hello akanksha --msg
   *   Display 'Hello Akanksha!' and a message.
   */
  public function commande($name = NULL, $options = [
    'dept' => FALSE,
    'contact' => FALSE,
    "adherent" => FALSE,
    "delcontact" => FALSE,
    'ville' => FALSE,
    'activites' => FALSE,
    'images' => FALSE,
    'repasse' => FALSE,
    'parent' => FALSE,
    'rubriques' => FALSE,
    'date' => FALSE,
    'activite' => FALSE,
    'unpublished' => FALSE,
    'geo' => FALSE,
    'import' => FALSE,
    'content' => FALSE,
    'url'=>FALSE,
    'geolocation'=>FALSE,
    'client' => FALSE,
    'filtres' => FALSE,
    'migrate' => FALSE,
    'delete' => FALSE,
    'rubrique_activite' => FALSE,
    'partage' => FALSE])
  {
    if ($name == "help") {
      echo "Migration de la base kidiklik : \n";
      echo "1er paramétre prend les valeurs client, adherent, contact, article, reportage, activite, agenda; jeu_concours\n";
      echo "les options possibles :\n";
      echo "- pour tous: --dept\n";
      echo "- pour client : --contact, --adherent, --delcontact\n";
      echo "- pour adherent : --contact, --delcontact\n";

    }else if ($name == "accueils") {
      if($options['import'] === true) {
        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
        $query2 = $connection->query("select * from accueils where id_accueil>56315");
        
        while($item=$query2->fetch()) {
          $node = Node::create([
            'type' => 'bloc_de_mise_en_avant',
            'title' => $this->propre($item->titre),
            'field_resume' => $this->propre($item->description), 
            'field_lien'=> $item->url,
            'field_type' =>  $item->type,
            'field_ref_accueil' => $item->id_accueil,
            'field_ref_entite' => $item->ref_entite,
            'field_ref_adherent' => $item->ref_adherent,
            'field_entite' => $item->entite,
            'field_image_save' => $item->image,
            'uid' => 1
          ]);
          $node->save();
          var_dump($item->titre);
          $dept = $item->dept;
          if($dept>=22) $dept--;
          
          if($dept !== 'NULL' && !empty($dept)) {
            var_dump($dept);
             $term_dep=current(\Drupal::entityTypeManager()
            ->getStorage("taxonomy_term")
            ->loadByProperties(['vid' => 'departement','name'=>$dept]));
            if(!empty($term_dep)) {
              $node->__set('field_departement',['target_id' => $term_dep->id()]);
              $node->save();
            }
          }
          var_dump($node->id());
        }

      } else if($options['date'] === true) {

     // }else {
        
        $connection = \Drupal::database();
        $query= $connection->select('node','n');
                $query->fields('n',['nid']);
                $query->join("node__field_ref_accueil","ra","ra.entity_id=n.nid");
                //$query->leftJoin("paragraph__field_date_de_fin","fdd","fdd.entity_id=fd.field_date_target_id");
                $query->fields('n',['nid']);
                $query->fields('ra',['entity_id', 'field_ref_accueil_value']);

                //$query->fields('fdd',['field_date_de_fin_value']);
                $query->condition('type','bloc_de_mise_en_avant','=');
                $query->condition('field_ref_accueil_value', 49587, '>');
                //$query->condition('field_date_de_debut_value','2019-01-01','<'); /* pour contenu article etc */
                //$query->condition('entity_id',NULL,'=');
                $rs=$query->execute();
                Database::setActiveConnection('kidiklik');
                $connection = \Drupal\Core\Database\Database::getConnection();

                while($item=$rs->fetch()) {

                  if((int)$item->field_ref_accueil_value>49587) {
                    var_dump($item->field_ref_accueil_value);
                    $node=Node::load($item->nid);
                    $node->__unset('field_date');
                    $node->save();
                    $accueil_id = $item->field_ref_accueil_value;
                    
                    if(!empty($accueil_id)) {
                      echo "select * from accueils_dates where ref_accueil='".$accueil_id."'\n";
                      $query2 = $connection->query("select * from accueils_dates where ref_accueil='".$accueil_id."'");
                     // var_dump($query2->fetchAll());
                      /*if(!count($query2->fetchAll())) {
                        var_dump('Suppression de la mise en avant');
                        $node->delete();
                      }*/
                      
                      while($bloc = $query2->fetch()) {
                        var_dump($bloc->date_debut);
                        $date = \Drupal\paragraphs\Entity\Paragraph::create([
                          'type' => 'date',
                          'field_date_de_debut' => [
                            'value' => $bloc->date_debut
                          ],
                          'field_date_de_fin' => [
                            'value' => $bloc->date_fin
                          ]
  
                        ]);
                        $date->save();
                        var_dump('Enregistrement de la date');
                        
                        $node->get('field_date')->appendItem($date);
                        $node->validate();
                        $node->save();
                      }
                    }
                   // exit;
                  }
                  //
                }
      }
      
    }else if ($name == "paragraphes") {
      if($options['repasse'] === true) {
        
        $connection = \Drupal::database();
        $query= $connection->select('paragraph__field_description','p')
            ->fields('p', ['entity_id','field_description_value'])
            ->condition('bundle','paragraphe','=');
        $rs= $query->execute();
        while($item=$rs->fetch()) {
          //preg_match('/&egrave;/',$item->field_description_value,$tb);
          $desc=str_replace('&eacute;','é',$item->field_description_value);
          $desc=str_replace('&egrave;','è',$desc);
          $desc=str_replace('&ocirc;','ô',$desc);
          $desc=str_replace('&acirc;','â',$desc);
          $desc=str_replace('&agrave','à',$desc);
          $desc=str_replace(['&lt;p&gt;','&lt;/p&gt;'],['',''],$desc);
          
          //$desc=addslashes($desc);
          /*$connection->update('paragraph__field_description')
          ->fields([
            'field_description_value'=> $desc
          ])
          ->condition('entity_id', $item->entity_id, '=')
          ->execute();*/
          $p=\Drupal\paragraphs\Entity\Paragraph::load($item->entity_id);
          $p->set('field_description', $desc);
          $p->save();
          var_dump($item->entity_id);
          //var_dump('update paragraph__field_description set field_description_value = "'.str_replace('&eacute;','è',$item->field_description_value).'" where entity_id='.$item->entity_id);
        }
        
      }
    } else if ($name == "taxonomy") {
      if($options['rubrique_activite'] === true) {
        $terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadByProperties(
          [
            'vid' => 'rubrique_activites'
          ]);

        foreach($terms as $term_id) {
          
          $term = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->load($term_id->id());
          $term->set('name', $this->propre($term->getName()));
          $term->save();
          var_dump($term->getName());
        }


      }
    }  else if($name == 'mise_en_avant') {
      /*$mea = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
        'type' => 'bloc_de_mise_en_avant'
      ]);*/
      $connection = \Drupal::database();

      $query= $connection->select('node','n')->fields('n',['nid'])
      ->condition('type','bloc_de_mise_en_avant','=')
      ->condition('nid',84158,'>');
        
      $rs=$query->execute();
      Database::setActiveConnection('kidiklik');
      while($item = $rs->fetch()) {
        $node = Node::Load($item->nid);
        
        $connection = \Drupal\Core\Database\Database::getConnection();
        if(!empty($node->get('field_ref_accueil')->value) && !empty($node)) {
          $query = $connection->query("select * from accueils where id_accueil = ".$node->get('field_ref_accueil')->value);
          
    
          $dept = $query->fetch()->dept;
          if($dept>=22) $dept--;
          
          if($dept !== 'NULL' && !empty($dept)) {
            var_dump($dept);
             $term_dep=current(\Drupal::entityTypeManager()
            ->getStorage("taxonomy_term")
            ->loadByProperties(['vid' => 'departement','name'=>$dept]));
            if(!empty($term_dep)) {
              var_dump($node->id());
              $node->__set('field_departement',['target_id' => $term_dep->id()]);
              $node->save();
            }
            


          
          }
         
        }
        
      }

    }else if ($name == "editos" || $name === "tests") {
      
      if($options['repasse']===TRUE) {
        $connection = \Drupal::database();
        if($name === 'editos') {
          $query= $connection->select('node','n')->fields('n',['nid'])->condition('type','article','=');
        }else {
          $query= $connection->select('node','n')->fields('n',['nid'])->condition('type','reportage','=');
        }
        
        $rs=$query->execute();

        while($node = $rs->fetch()) {
          $n=Node::load((int)$node->nid);
          var_dump($n->id());
          $n->set('field_type_reportage', false);
          $n->save();
        }
      } else if($options['delete']===TRUE) {
        /*$nodes=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
          'type' => 'article',
        ]);*/

        $connection = \Drupal::database();
        if($name === 'editos') {
          $query= $connection->select('node','n')->fields('n',['nid'])->condition('type','article','=');
        }else {
          $query= $connection->select('node','n')->join('node__field_type_reportage', 't', 'n.nid = t.entity_id')->fields('n',['nid'])
          ->condition('type','reportage','=')
          ->condition('field_type_reportage_value','1','=');
        }
        
        $rs=$query->execute();

        while($node = $rs->fetch()) {
          $n=Node::load((int)$node->nid);
          var_dump('SUPP: '.$n->id());
          $n->delete();
        }

      } else 
      /**
        * MIGRATE EDITOS AND REPORTAGES 
       */
      if($options['migrate']===TRUE || $options['import']===TRUE) {
        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
        $sql = "select * from ".$name." where date_fin >= '2020-01-01'";
        if($name == "editos") {
          $sql.=" and id_edito > 6767";
        } else {
          $sql.=" and id_test > 1012";
        }
        $query = $connection->query($sql);
       
        while($edito=$query->fetch()) {
          $dept=(int)$edito->dept;
          if($dept>=22) $dept--;

          $adherent = null;
          if($edito->ref_adherent !== null) {
            $adherent = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
              'type' => 'adherent',
              'field_ref_adherent' => $edito->ref_adherent
            ]));
            $adherent_id = null;
            if($adherent !== false) {
              $adherent_id = $adherent->id();
            }
            
          }

         
          $node = Node::create([
            'type' => 'article',
            'title' => $this->propre($edito->titre),
            'body' => $this->propre($edito->resume), 
            'field_video'=> $edito->video,
            'field_google_map' =>  $edito->map,
            'field_ref_adherent' => $edito->ref_adherent,
            'field_type_reportage' => ($name==='editos'?0:1),
            'field_ref_entite' => ($name==='editos'?$edito->id_edito:$edito->id_test),
            'uid' => 1
          ]);
          var_dump($edito->titre);

          $node->save();
          $term_dep=current(\Drupal::entityTypeManager()
            ->getStorage("taxonomy_term")
            ->loadByProperties(['vid' => 'departement','name'=>$dept]));

          if($term_dep !== false) {
            $node->__set('field_departement',$term_dep->id());
          }
          if($adherent_id !== null) {
            $node->__set('field_adherent', $adherent_id);
          }
          

          $date = \Drupal\paragraphs\Entity\Paragraph::create([
            'type' => 'date',
            'field_date_de_debut' => [
              'value' => $edito->date_debut
            ],
            'field_date_de_fin' => [
              'value' => $edito->date_fin
            ]
          ]);
          //$date->save();
          $node->get('field_date')->appendItem($date);

          if($name === 'editos') {
            //$node->__set('field_ref_article' , $edito->id_edito);
             $query2 = $connection->query("select nom from asso_rubriques_editos are join editos_rubriques er on er.id_rubrique = are.ref_rubrique where are.ref_edito =".$edito->id_edito);
            while($rub_edito = $query2->fetch()) {
              
              $term = current(\Drupal::entityTypeManager()
              ->getStorage('taxonomy_term')
              ->loadByProperties(
                [
                  'vid' => 'rubrique_article',
                  'name' => $rub_edito->nom
                ]));
              
                if(!empty($term)) {
                  var_dump("RUB : ".$term->getName());
                  $node->get('field_rubriques')->appendItem($term->id());
                }
              
            }
         
          } else {
            //$node->__set('field_ref_reportage' , $edito->id_test);
          }


          if($name === 'editos') {
            $query3 = $connection->query("select * from editos_datas  where ref_edito =".$edito->id_edito);
          } else {
            $query3 = $connection->query("select * from tests_datas  where ref_test =".$edito->id_test);
          }
          while($para_edito = $query3->fetch()) {
            $desc=$this->propre($para_edito->description);
            
            $paragraph = \Drupal\paragraphs\Entity\Paragraph::create([
              'type' => 'paragraphe',
              'field_titre' => [
                'value' => $this->propre($para_edito->titre)
              ],
              'field_description' => [
                'value' => $desc
              ],
              'field_url'=> [
                'value' => $para_edito->url??null
              ],
              'field_image_save'=> [
                'value' => $para_edito->image
              ],
            ]);
            $node->get('field_paragraphes')->appendItem($paragraph);
            //$paragraph->save();
          }

          $node->save();
        
        }
      }
    }else if ($name == "adherents") { 
      Database::setActiveConnection('kidiklik');
      $connection = \Drupal\Core\Database\Database::getConnection();
      if($options['import'] === true) {
        $query = $connection->query("select * from adherents where id_adherent > 6926");
        while($row = $query->fetch()) {
          $query2 = $connection->query("select * from villes where id_ville = :ville", [
            ':ville' => $row->ref_ville
          ]);
          $row2=$query2->fetch();
          var_dump($row->nom);
          $dept=(int)$row->dept;
          if($dept>=22) $dept--;
  
          $node=Node::Create([
            'type' => 'adherent',
            'title' => $this->propre($row->nom),
            'field_adresse' => $this->propre($row->adresse),
            'field_ref_ville' => $row->ref_ville,
            'field_email' => $this->propre($row->email),
            'field_lien' => $this->propre($row->url),
            'field_note' => $this->propre($row->note),
            'field_ref_dept' => $row->dept,
            'field_telephone' => $this->propre($row->telephone),
            'field_ref_client' => $row->ref_client,
            'field_ref_adherent' => $row->id_adherent,
            'field_ville_save' => $this->propre($row2->commune),
            'field_code_postal' => $this->propre($row2->code_postal),
          ]);
          $node->save();
          $term_dep=current(\Drupal::entityTypeManager()
              ->getStorage("taxonomy_term")
              ->loadByProperties(['vid' => 'departement','name'=>$dept]));
          if($term_dep !== false) {
            $node->__set('field_departement',$term_dep->id());
          }
          $contact = current(\Drupal::entityTypeManager()
          ->getStorage("node")->loadByProperties([
            'type' => 'contact',
            'field_ref_adherent' => $row->id_adherent
          ]));
          if(!empty($contact)) {
            var_dump("CONTACT: ".$contact->getTitle());
            $node->__get('field_contact')->appendItem($contact);
          }

          $node->save();
        }
      }

    }else if ($name == "contacts") { 
      Database::setActiveConnection('kidiklik');
      $connection = \Drupal\Core\Database\Database::getConnection();
      if($options['adherent'] === true) {
        $type = 'adherents';
      } 
      if($options['clients'] === true) {
        $type = 'clients';
      } 
      if($options['import'] === true) {
        $query = $connection->query("select * from ".$type."_contacts");
        while($row = $query->fetch()) {
          $dept=(int)$row->dept;
          if($dept>=22) $dept--;
          $query2 = $connection->query("select * from villes where id_ville = :ville", [
            ':ville' => $row->ref_ville
          ]);
          $row2=$query2->fetch();
          var_dump($row->nom);
          $dept=(int)$row->dept;
          if($dept>=22) $dept--;

          $tab = [
            'type' => 'contact',
            'title' => $this->propre($row->nom),
            'field_email' => $this->propre($row->email),
            'field_type_contact' => ($type==='clients'?'client':'adherent'),
            'field_ref_dept' => $row->dept,
            'field_telephone' => $this->propre($row->telephone),
          ];
          if($type === 'clients') {
            $tab[ 'field_ref_client'] = $row->ref_client;
          }
          if($type === 'adherents') {
            $tab[ 'field_ref_adherent'] = $row->ref_adherent;
          }
          if(!empty($row->nom)) {
            $node=Node::Create($tab);
            $node->save();
            $term_dep=current(\Drupal::entityTypeManager()
                ->getStorage("taxonomy_term")
                ->loadByProperties(['vid' => 'departement','name'=>$dept]));
            if($term_dep !== false) {
              $node->__set('field_departement',$term_dep->id());
            }
            $node->save();
          }
          
        }
      }

    } else if ($name == "dept") {
        if($options['import'] === TRUE) {
            Database::setActiveConnection('kidiklik');
            $connection = \Drupal\Core\Database\Database::getConnection();
            $query = $connection->query("select * from departements order by id_departement");
            while($row=$query->fetch()) {
                $t=current(\Drupal::entityTypeManager()
                  ->getStorage('taxonomy_term')
                  ->loadByProperties([
                  'vid' => 'departement',
                  'name' => $row->code
                  ]));
                  if(!empty($t)) {
                    $t->delete();
                  }
                  
                var_dump($row->id_departement);
                $query2=$connection->query("select * from coordonnees where dept=".$row->id_departement);
                $row2=$query2->fetch();
                
                var_dump($row2->societe);
                $new_term=Term::create([
                  'vid' => 'departement',
                  'name' => $row->code,
                  'field_nom' => $row->nom,
                  'field_couleur' => $row->couleur,
                  'field_ref_dept' => $row->id_departement,
                  'field_ref_ville' => $row2->ref_ville,
                  'field_adresse' => ($row2->adresse === 'NULL' ? null:$row2->adresse),
                  'field_telephone' => ($row2->telephone === 'NULL' ? null:$row2->telephone),
                  'field_e_mail' => ($row2->email === 'NULL' ? null:$row2->email),
                  'field_societe' => ($row2->societe === 'NULL' ? null:$row2->societe),
                ]);
                $new_term->enforceIsNew();
                $new_term->save();
            }
            
        } else {
          $rs = \Drupal::entityTypeManager()
            ->getStorage("taxonomy_term")
            ->loadByProperties(["vid" => "departement"]);
          foreach ($rs as &$item) {
            //kint($item);
            if ($item->get("field_code")->value) {
              $item->set("field_ref_dept", $item->getName());
              $item->setName($item->get("field_code")->value);
              //
              $item->save();
            }

          }
      }


    }/* elseif($name === 'kidi_agendas') {

      $connection = \Drupal::database();
      $query= $connection->select('node__field_ref_agenda','n')
            ->fields('n', ['entity_id', 'field_ref_agenda_value'])
            ->condition('entity_id','154588','>=')
            ->condition('bundle','agenda','=')
            ->execute()->fetchAll();

      Database::setActiveConnection('kidiklik');
      $connection = \Drupal\Core\Database\Database::getConnection();
      foreach($query as $item) {

        $n = Node::load((int)$item->entity_id);
       // var_dump($n->get('field_ref_agenda')->value);
        $query2 = $connection->query("select * from agendas_dates where ref_agenda = '" . $item->field_ref_agenda_value . "'");
        while($rs = $query2->fetch()) {
          $date = \Drupal\paragraphs\Entity\Paragraph::create([
            'type' => 'date',
            'field_date_de_debut' => [
              'value' => $rs->date_debut
            ],
            'field_date_de_fin' => [
              'value' => $rs->date_fin
            ]

          ]);
          var_dump("add date : ");
          var_dump($rs);
          $date->save();
          $n->get('field_date')->appendItem($date);
        }
        $n->validate();
        $n->save();

      }

    }*/ elseif ($name === 'kidi_activites' || $name === 'kidi_agendas') {
      /**
       * TRAITEMENT ACTIVITES : images, dates
       */
      Database::setActiveConnection('kidiklik');
      $connection = \Drupal\Core\Database\Database::getConnection();
      if($name === 'kidi_activites') {
        $query = $connection->query("select * from activites where id_activite > 5155");
      } else {
        $query = $connection->query("select * from agendas where id_agenda > 56803");
      }

     /* $result = \Drupal::entityQuery('node')
        ->condition('field_ref_parent', '670', '=')
        ->condition('vid', 'rubrique_activites')
        ->execute();*/

      while ($content = $query->fetch()) {
        var_dump($content->id_activite);
        if($name === 'kidi_activites') {
          $params=[
            'type' => 'activites',
            'field_ref_activite' => $content->id_activite
          ];
        } else {
          $params=[
            'type' => 'agenda',
            'field_ref_agenda' => $content->id_agenda
          ];
        }
        var_dump($params);

        $node = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties($params));
       
        if(!empty($node)) {
          var_dump('NODE : ' . $node->id() );
        }
        //var_dump($node->id());exit;
        if ($options['import'] === true && empty($node)) {
          if($name ==="kidi_activites") {

            $tab = [
              'type' => 'activite',
              'title' => $this->propre($content->titre),
              'field_adresse' => $this->propre($content->adresse),
              'field_ref_ville' => $content->ref_ville,
              'field_duree'=> $content->duree,
              'field_ref_dept' => $content->dept,
              'field_resume' => $this->propre($content->resume,true),
              'body' => $this->propre($content->description,true),
              'field_ref_google_map' => $content->map,
              'field_lieu' => $this->propre($content->lieu),
              'field_telephone' => $content->telephone,
              'field_email' => $content->email,
              'field_lien' => $content->url,
              'field_a_partir_de' => $content->age_min,
              'field_jusque_age_de' => $content->age_max,
              'field_tarifs' => $this->propre($content->tarifs,true),
              'field_horaires' => $this->propre($content->horaires,true),
              'field_duree' => $this->propre($content->duree),
              'field_info_complementaires'=>$this->propre($content->info_sup),
              'field_services' => $this->propre($content->service),
              'field_type_de_reservation' => $content->type_resa,
              'field_ref_adherent' => $content->ref_adherent,
              'field_ref_activite' => $content->id_activite,
              'field_reservation' => $this->propre($content->reservation),
              'field_longitude' => $content->lng,
              'field_latitude' => $content->lat,
            ];
            
          } else {
            $tab = [
              'type' => 'agenda',
              'title' => $this->propre($content->titre),
              'field_adresse' => $this->propre($content->adresse),
              'field_ref_ville' => $content->ref_ville,
              'field_duree'=> $content->duree,
              'field_ref_dept' => $content->dept,
              'field_resume' => $this->propre($content->resume,true),
              'body' => $this->propre($content->description,true),
              'field_ref_google_map' => $content->map,
              'field_lieu' => $this->propre($content->lieu),
              'field_telephone' => $content->telephone,
              'field_email' => $content->email,
              'field_lien' => $content->url,
              'field_a_partir_de' => $content->age_min,
              'field_jusque_age_de' => $content->age_max,
              'field_tarifs' => $this->propre($content->tarifs,true),
              'field_horaires' => $this->propre($content->horaires,true),
              'field_duree' => $this->propre($content->duree),
              'field_info_complementaires'=>$this->propre($content->info_sup),
              'field_services' => $this->propre($content->service),
              'field_type_de_reservation' => $content->type_resa,
              'field_ref_adherent' => $content->ref_adherent,
              'field_ref_activite' => $content->ref_activite,
              'field_ref_agenda' => $content->id_agenda,
              'field_reservation' => $this->propre($content->reservation),
              'field_longitude' => $content->lng,
              'field_latitude' => $content->lat,
            ];
          }
          $node= Node::Create($tab);
          $node->enforceIsNew();
          $node->save();
          var_dump($node->getTitle());
          //$node = node::Load($node->id());
        } //else {
          
        
          //if($node->id() > 45710)
          

          // var_dump(current($node->get('field_departement')->getValue())['target_id']);
            if ($options['images'] === true) {
              if ($name === 'kidi_activites') {
                $query2 = $connection->query("select * from activites_galeries where ref_activite = '" . $content->id_activite . "'");
              } else {
                $query2 = $connection->query("select * from agendas_galeries where ref_agenda = '" . $content->id_agenda . "'");
              }

              while ($image = $query2->fetch()) {

                if ($node->get('field_image_save')->isEmpty()) {
                  var_dump('insert image : ' . $image->image);
                  try {
                    $node->get('field_image_save')->appendItem($image->image);
                    $node->validate();
                    $node->save();
                  } catch (\Exception $e) {
                    var_dump($e->getMessage());
                  }

                }

              }
            }elseif ($options['rubrique'] === true) {
                //SELECT r.nom, r.dept FROM `asso_rubrique_activitess` ra join rubriques r on r.id_rubrique = ra.ref_rubrique
                $query_rub = $connection->query("select * from asso_rubrique_activitess where ref_activite='" . $content->id_activite . "'");

            }
            if ($options['date'] === true) {
              /* traitement dates des activités */

                echo "Node date : " . $node->id() . "\n";
                if($name === 'kidi_activites') {
                 /* $pattern=['/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/'];
                  $replace=['\4/\3/\1\2'];
                  $date_deb=preg_replace($pattern, $replace, $content->date_debut);
                  $date_fin=preg_replace($pattern, $replace, $content->date_fin);*/
                  $date = \Drupal\paragraphs\Entity\Paragraph::create([
                    'type' => 'date',
                    'field_date_de_debut' => [
                      'value' => $content->date_debut
                    ],
                    'field_date_de_fin' => [
                      'value' => $content->date_fin
                    ]

                  ]);
                  $date->save();
                  $node->__unset('field_date');
                  $node->get('field_date')->appendItem($date);
                  //var_dump(current($node->get('field_date')->getValue())->id());
                  $node->save();
                } else {
                  $query_date = $connection->query("select * from agendas_dates where ref_agenda='" . $content->id_agenda . "'");
                  if(empty($node->get('field_date')->getValue())) {
                    while ($agenda_date = $query_date->fetch()) {
                      $date = \Drupal\paragraphs\Entity\Paragraph::create([
                        'type' => 'date',
                        'field_date_de_debut' => [
                          'value' => $agenda_date->date_debut
                        ],
                        'field_date_de_fin' => [
                          'value' => $agenda_date->date_fin
                        ]

                      ]);
                      var_dump("add date : ");
                      var_dump($agenda_date);
                      $date->save();
                      $node->get('field_date')->appendItem($date);

                    }
                    $node->save();
                  }

                }



            }
            if ($options['geo'] === true) {
              /* traitement localise activite */
                var_dump('insert lat/lng : ' . $content->lat . "/" . $content->lng . "...");

                $node->__set('field_geolocation_demo_single', ['lat' => $content->lat, 'lng' => $content->lng]);
                $node->__set('field_geolocalisation', ['lat' => $content->lat, 'lng' => $content->lng]);
                $node->__set('field_latitude', $content->lat);
                $node->__set('field_longitude', $content->lng);
                $node->save();
                echo "OK\n";

            }
        //}
       // exit;
      }
    } else if ($name === 'rubriques_articles') {
      if($options['import'] === TRUE) {
        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
        $query = $connection->query("select * from editos_rubriques");
        while($row = $query->fetch()) {
          $rub = Term::Create([
            'vid' => 'rubrique_article',
            'name' => $this->propre($row->nom),
            'field_edito' => $this->propre($row->edito),
            'field_seo' => $this->propre($row->seo),
            'field_ref_rubriques_article' => $row->id_rubrique
          ]);
          $rub->save();
          var_dump($row->nom);
        }
      }
      
    }else if ($name === 'rubrique_activites') {
      /**
       * TRAITEMENT RUBRIQUES DES ACTIVITES
       */
      echo "Traitement rubrique ... ";
      if((bool)$options['import'] === true) {
        Database::setActiveConnection('kidiklik');
        $onnexion = \Drupal\Core\Database\Database::getConnection();
        $query=$onnexion->query("select * from rubriques order by ordre");

        while($row=$query->fetch()) {

          $new_term=Term::create([
            'vid' => 'rubrique_activites',
            'name' => $this->propre($row->nom),
            'field_description_seo' => $this->propre($row->seo),
            'field_ref_dept' => $row->dept,
            'field_ref_parent' => $row->parent,
            'field_ref_rubrique' => $row->id_rubrique,
            'field_titre_seo' => $this->propre($row->titre_seo),
          ]);
          var_dump($new_term->getName());
          $new_term->enforceIsNew();
          $new_term->save();
        }
        /*
        $new_term->enforceIsNew();
        $new_term->save();*/
      }else if((bool)$options['dept'] === true) {
        $rubriques = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(
            [
              "vid" => "rubrique_activites",
            ]
          );
        foreach ($rubriques as $rubrique) {

          if (!empty((int)$rubrique->get('field_ref_dept')->value)) {
            $dept = current(\Drupal::entityTypeManager()
              ->getStorage('taxonomy_term')
              ->loadByProperties(
                [
                  "field_ref_dept" => (int)$rubrique->get('field_ref_dept')->value,
                  "vid" => "departement",

                ]
              ));
            var_dump($rubrique->id());
            if(!empty($dept)) {
              var_dump($dept->id());
              $rubrique->set('field_departement', $dept);
              $rubrique->validate();
              $rubrique->save();
            }
;
          }

        }
      } elseif((bool)$options['unpublished'] === true) {
        $result = \Drupal::entityQuery('taxonomy_term')
          ->condition('field_ref_parent', '670', '=')
          ->condition('vid', 'rubrique_activites')
          ->execute();
        $rubriques = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadMultiple($result);
        foreach ($rubriques as $rubrique) {
          var_dump($rubrique->getName());
          if ((int)$rubrique->get('field_ref_parent')->value === 670) {
            $rubrique->setUnpublished();
            $rubrique->save();
          }
        }
      }elseif((bool)$options['parent'] === true) {

        $result = \Drupal::entityQuery('taxonomy_term')
          ->condition('field_ref_parent', '0', '>')
          //->condition('tid', '1564', '=')
          ->condition('vid', 'rubrique_activites')
          ->execute();
        $rubriques = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadMultiple($result);

        foreach ($rubriques as $rubrique) {
          var_dump($rubrique->get('field_ref_parent')->value);
          if((int)$rubrique->get('field_ref_parent')->value === 670) {
            $rubrique->setUnpublished();
            $rubrique->save();
          }
          $parent = current(\Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(
              [
                "vid" => "rubrique_activites",
                "field_ref_rubrique" => $rubrique->get('field_ref_parent')->value
              ]
            ));

          if(!empty($parent)) {
            var_dump('save');
            $rubrique->set('parent', $parent->id());
            $rubrique->save();
            //var_dump($parent->id());
          } else {
            var_dump('error');
            var_dump($rubrique->get('field_ref_parent')->value);
          }



        }
      } elseif((bool)$options['repasse'] === true) {
        $rubriques = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(
            [
              "vid" => "rubriques_activite",
          ]);
        foreach ($rubriques as $rubrique) {
          $rubrique->setName($this->propre($rubrique->getName()));
          $rubrique->save();
          var_dump($rubrique->getName());
          /*var_dump($rubrique->id());
          var_dump($rubrique->get('field_ref_parent')->value);
          var_dump($rubrique->get('field_ref_rubrique')->value);*/
        }

      }



    } else if ($name === 'ville') {

      $query = $this->connection->query("select * from villes");
      while ($ville = $query->fetch()) {
        //var_dump(html_entity_decode($ville->commune));
        $this->connection->query("update villes set commune = \"" . str_replace("&#39;", "'", $ville->commune) . "\" where id_ville=" . $ville->id_ville);
      }

      /**
       * node content
       */
    } elseif($name === 'user') {
      if($options['content'] === true) {

        $database = \Drupal::database();
        $user_query = $database->select('user__field_departement','u');
        $user_query->join("user__roles","ur","ur.entity_id=u.entity_id");
        $user_query->fields('u', ['entity_id','field_departement_target_id']);
        $user_query->condition('u.entity_id',107,'>=');
        $user_query->condition('ur.roles_target_id','editeur','=');
        $users=$user_query->execute();
        
        while($user=$users->fetch()) {
          $term_dept = $user->field_departement_target_id;
          var_dump('DEP : '.$term_dept);

          $query=$database->select("node__field_departement","n");
          $query->fields("n",["entity_id","field_departement_target_id"]);
          $query->condition("n.field_departement_target_id",$term_dept,"=");
          $rs=$query->execute();
          while($item = $rs->fetch()) {
            var_dump('USER : '.$user->entity_id.' ADD NODE : '.$item->entity_id);
            $n=Node::load($item->entity_id);
            $n->setOwnerId($user->entity_id);
            $n->save();
          }
        }
      } elseif($options['repasse'] === true) {

          //$tmp=\Drupal::entityQuery('user')->execute();
          $list = [];
          Database::setActiveConnection('kidiklik');
          $connection = \Drupal\Core\Database\Database::getConnection();
          $query = $connection->query('select * from utilisateurs where ref_profil=5');
          foreach($query as $item) {
            try {
              $user=current(\Drupal::entityTypeManager()->getStorage("user")->loadByProperties([
                'mail' => $item->email
              ]));//
              //var_dump($user->mail);
             // $mail = current($user->get('mail')->getValue())['value'];
             // $query = $connection->query('select * from utilisateurs where email = "'.$mail.'"')->fetch();
              if(!empty($user)) {
                var_dump($item->email);
                var_dump('add role :'.$item->ref_profil);
                switch($item->ref_profil) {
                  case 2: // admin
                    $user->addRole('administrateur_de_departement');
                    break;
                  case 3: // redactediteureur
                    $user->addRole('editeur');
                    break;
                  case 4: // redacteur
                    $user->addRole('redacteur');
                    break;
                  case 5: // redacteur
                    $user->set('roles',array_unique(['authenticated']));
                    break;
                }
                $user->activate();
                $user->save();
              }
            }catch(Exception $e) {
              var_dump($e->getMessage());

            }

            //

          }




      }else if($options['import'] === true) {
        $language='fr';
        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
        $query = $connection->query('select u.*, d.code from utilisateurs u join departements d on d.id_departement=u.dept');
        while($ku=$query->fetch()) {

          try {
            if(!empty($ku->dept) && $ku->dept!==NULL) {
              $dept = (int)$ku->code;//($ku->dept<22?$ku->dept:$ku->dept-1);
              $term_dept = get_term_departement($dept);
            } else {
              $term_dept = get_term_departement(0);
            }


            $test=\Drupal::entityTypeManager()->getStorage("user")->loadByProperties(
              [
                'name' => $ku->login
              ]
             );

            if(!count($test)) {
              var_dump('add user :'.$ku->login);
              $user = \Drupal\user\Entity\User::create();
              $user->setPassword(strtolower($ku->prenom.$ku->nom));
              $user->setEmail($ku->email);
              $user->setUserName($ku->login);
              $user->set('field_nom',$ku->nom);
              $user->set('field_prenom',$ku->prenom);
              $user->set('field_departement',$term_dept);
              $user->set('field_administrateur_dep',$dept);
              $user->set("init", 'mail');
              $user->set("langcode", 'fr');
              $user->set("preferred_langcode", $language);
              $user->set("preferred_admin_langcode", $language);

              switch($ku->ref_profil) {
                case 2: // admin
                  $user->addRole('administrateur_de_departement');
                  break;
                case 3: // redactediteureur
                  $user->addRole('editeur');
                  break;
                case 4: // redacteur
                  $user->addRole('redacteur');
                  break;
                case 5: // redacteur
                  $user->set('roles',array_unique(['authenticated']));
                  break;
              }
              $user->activate();
              $user->save();
            }
          } catch(Exception $e) {
            var_dump($e->getMessage());

          }



        }
      }


      //

    }  else if ($name) {

      if($options['delete'] === true) {
        echo $name;
        $connection = \Drupal::database();
                $query= $connection->select('node','n');
                $query->fields('n',['nid']);
                //$query->join("node__field_date","fd","fd.entity_id=n.nid");
                //$query->join("paragraph__field_date_de_fin","fdd","fdd.entity_id=fd.field_date_target_id");
                //$query->fields('n',['nid']);
                //$query->fields('fd',['entity_id']);
                //$query->fields('fdd',['field_date_de_fin_value']);
                $query->condition('type',$name,'=');
                $query->condition('nid',168785,'>=');
                //$query->condition('field_date_de_debut_value','2019-01-01','<'); /* pour contenu article etc */
                //$query->condition('field_date_de_fin_value','2020-01-01','<');
                $rs=$query->execute();
                while($item=$rs->fetch()) {
                  $node=Node::load($item->nid);
                  if(!empty($node)) {
                    var_dump($node->getTitle());
                    $node->delete();
                    
                  }
                 // 
                  //var_dump($node->get('field_date')->getValue());
                  

                }
                
      } else {
        $connection = \Drupal::database();
        $rs = $connection->query('select * from node where  type=:type order by nid', [
          ':type' => $name,
        ], [
          'fetch' => 'node'
        ]);
        
      }
      /**
       * TRAITEMENT PAR TYPE DE CONTENU
       */

      
      if($options['filtres'] === true) {
        $filtres = [];
        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
        $query = $connection->query("select * from filtres");

        while($item = $query->fetch()) {

          $filtres[]=$item;
        }
      }

      while ($result = $rs->fetchObject()) {

        $nid = $result->nid;

        var_dump("Chargement  '$name : $result->nid' ...");

        $item = Node::Load($nid);
        /*Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
        $query = $connection->select('agendas','a')
        ->fields('a',['resume', 'description'])
        ->condition('a.id_agenda',$item->get('field_ref_agenda')->value, '=')
        ->execute()
        ->fetch();*/
        $item->__set('title', $this->propre($item->getTitle()));

        $item->__set('body', $this->propre($item->get('body')->value));
        //$item->validate();
        //$item->save();
        //var_dump($query);

        // var_dump($item);
        foreach ($options as $key => $option) {

          if ($option) {

            switch ($key) {
              case 'partage':
                Database::setActiveConnection('kidiklik');
                $connection = \Drupal\Core\Database\Database::getConnection();
                $item->__unset('field_partage_departements');
                $item->save();
                switch($name) {
                  case 'article':
                    $id_entity = current($item->get('field_ref_entite')->getValue())['value'];
                    break;
                  case 'activite':
                    $id_entity = current($item->get('field_ref_activite')->getValue())['value'];
                    break;
                  case 'agenda':
                      $id_entity = current($item->get('field_ref_agenda')->getValue())['value'];
                      break;
                }  
                $query = $connection->select('partages','a')
                    ->fields('a',['ref_id', 'dept', 'to_dept'])
                    ->condition('a.ref_id', $id_entity, '=')
                    ->execute()->fetch();

                    if($query->to_dept !== NULL && $query->to_dept !== 'NULL') {
                        if($query->to_dept === 0 || $query->to_dept === '0') {
                            $query = \Drupal::entityQuery('taxonomy_term')
                            ->condition('status', '1', '=')
                            ->condition('vid', 'departement');
                            
                            $depts = Term::loadMultiple($query->execute());
                            foreach($depts as $dept) {
                              
                              $item->__get('field_partage_departements')->appendItem([
                                'target_id' => $dept->id()
                              ]);
                            }
                            $item->save();
                          } else {
                            if($query->to_dept >= 22) {
                              $to_dept = $query->to_dept;
                              $to_dept--;
                            } else {
                              $to_dept = $query->to_dept;
                            }


                            $term_dept = get_term_departement($to_dept);

                            if(!empty($term_dept)) {
                              $dept = Term::load($term_dept);
                              $item->__set('field_partage_departements', $dept);
                              $item->save();
                            }
                            
                          }
                    }    

                break;
              case 'delete':
                $connection = \Drupal::database();
                $query= $connection->select('node','n');
                $query->join("node__field_date","fd","fd.entity_id=n.nid");
                //$query->join("paragraph__field_date_de_fin","fdd","fdd.entity_id=fd.field_date_target_id");
                $query->fields('n',['nid']);
                $query->fields('fd',['entity_id']);
                $query->fields('fdd',['field_date_de_fin_value']);
                $query->condition('type','activite','=');

                //$query->condition('field_date_de_fin_value','2021-01-01','<=');
                $rs=$query->execute();
                while($item=$rs->fetch()) {
                  $node=Node::load($item->nid);
                  if(!empty($node)) {
                    $node->delete();
                    var_dump($node->id());
                  }

                }
               // var_dump("DELETE : ".$item->id());
               // $item->delete();

                break;
              case 'filtres':
                Database::setActiveConnection('kidiklik');
                $connection = \Drupal\Core\Database\Database::getConnection();
               var_dump($item->get('field_ref_'.$name)->value);
                if(!empty($item->get('field_ref_'.$name)->value)) {
                  $item->__unset('field_filtres');
                  $item->save();
                  $content_filtres= [];
                  foreach($filtres as $filtre) {
                    var_dump("select * from entite_filtre_".$filtre->identifiant."_valeur where entite='".$name."' and ref_entite=".$item->get('field_ref_'.$name)->value);
                    $query = $connection->query("select * from entite_filtre_".$filtre->identifiant."_valeur where entite='".$name."' and ref_entite=".$item->get('field_ref_'.$name)->value);
                    while($content = $query->fetch()) {
                      $content_filtres[$filtre->identifiant][] = $content->valeur;

                    }

                    /**/
                  }
                  if(!empty($content_filtres)) {

                    $filtre = \Drupal\paragraphs\Entity\Paragraph::create([
                      'type' => 'filtres',
                      'field_envies' => [
                        'value' => (isset($content_filtres['envie'])?($content_filtres['envie'][0]):null)
                      ],
                      'field_thematiques' => [
                        'value' => (isset($content_filtres['thematique'])?($content_filtres['thematique'][0]):null)
                      ],
                    ]);
                    $filtre->save();
                    foreach($content_filtres['vacances'] as $val) {
                      $filtre->get('field_vacances')->appendItem($val);
                    }
                    foreach($content_filtres['tranches_age'] as $val) {
                     // var_dump($val);
                      preg_match("/([0-9]*)-([0-9]*)ans/",$val,$match);
                      if(!empty($match)) {
                     //   $val=$match[1]."-".$match[2]."ans";
                        //$filtre->get('field_tranches_d_ages')->appendItem($val);
                      }
                      $filtre->get('field_tranches_d_ages')->appendItem($val);

                    }
                    $filtre->save();
                    $filtre->validate();
                    $item->get('field_filtres')->appendItem($filtre);
                      $item->validate();
                      $item->save();
                   // var_dump($content_filtres);
                  // exit;
                  }
                }

                break;
              case 'geolocation':


                //if(empty($item->get("field_geolocation_demo_single")->value) || $item->get("field_geolocation_demo_single")->value==='NULL') {
                  $ville=KidiklikEntity::getGPS($item->get('field_ville_save')->value);
                  var_dump($item->get("field_geolocation_demo_single")->value);
                  //var_dump($ville);
                  /*Database::setActiveConnection('kidiklik');
                  $connection = \Drupal\Core\Database\Database::getConnection();
                  $query=$connection->query()*/

                  if($ville['lat'] !== '0' && $ville['lng'] !=='0') {
                    $item->set("field_geolocation_demo_single",[
                      "lat"=>$ville["lat"],
                      "lng"=>$ville["lng"]
                    ]);
                    $item->validate();
                    $item->save();
                  }
               // }
                break;

              case 'url':
                switch($name) {
                  case 'bloc_de_mise_en_avant':
                    $id_entite = current($item->get('field_ref_entite')->getValue())['value'];
                    $lien = current($item->get('field_lien')->getValue())['value'];
                    preg_match('/([https:\/\/])([0-9]{2}).kidiklik.fr\/(.*)\/([0-9]*)-(.*).html/',$lien,$match);
                    //var_dump(current($item->get('field_lien')->getValue())['value']);
                   
                    if(!empty($id_entite) && count($match)) {
                      $node=current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                        'type' => 'article',
                        'field_ref_entite' => $id_entite
                      ]));
                      if(!empty($node)) {
                        var_dump($node->getTitle());
                        var_dump($lien);
                        $new_link=\Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$node->Id());
                        var_dump($new_link);
                        $item->__set('field_lien',$new_link);
                        $item->validate();
                        $item->save();
                      }
                      
                    }
                    
                    break;

                }
                /*$lien = current($item->get('field_lien')->getValue())['value'];

                if(!empty($lien)) {
                  preg_match('/([https:\/\/])([0-9]{2}).kidiklik.fr\/(.*)\/([0-9]*)-(.*).html/',$lien,$match);
                  var_dump($match);
                  if(count($match)) {
                    $connection = \Drupal\Core\Database\Database::getConnection();
                    //var_dump($match);
                    switch($match[3]) {
                      case 'articles':
                        $node=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                          'type' => 'article',
                          'field_ref_activite' => $item->get("field_ref_activite")->value
                        ]));
                        break;
                      case 'sorties-moment':
                        if(!empty($match[4])) {
                          var_dump($match[4]);
                          $node=current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                            'type' => 'agenda',
                            'field_ref_agenda' => $match[4]
                          ]));
                          if(!empty($node)) {
                            var_dump($node->Id());
                            $new_link=\Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$node->Id());
                            var_dump($new_link);
                            $item->__set('field_lien',$new_link);
                             $item->validate();
                            $item->save();
                          }

                         // $query=$connection->query('update node__field_lien set field_lien_value="'.$new_link.'" where entity='.$item->id());

                        }

                        break;
         
                      default:
                        break;

                    }
                  }

                }*/

                break;
              case 'date':
                $item->__unset('field_date');
                if($name === 'bloc_de_mise_en_avant') {
                  $accueil_id = current($item->get('field_ref_accueil')->getValue())['value'];

                  if(!empty($accueil_id)) {
                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
                    $query = $connection->query("select * from accueils_dates where ref_accueil='".$accueil_id."'");
                    while($bloc = $query->fetch()) {
                      $date = \Drupal\paragraphs\Entity\Paragraph::create([
                        'type' => 'date',
                        'field_date_de_debut' => [
                          'value' => $bloc->date_debut
                        ],
                        'field_date_de_fin' => [
                          'value' => $bloc->date_fin
                        ]

                      ]);
                      $date->save();
                      var_dump('Enregistrement de la date');
                      var_dump($bloc->date_fin);

                      $item->get('field_date')->appendItem($date);
                      $item->validate();
                      $item->save();
                    }
                  }
                }
                break;

              case "images": /* bloc_de_mise_en_avant*/
                if($name === 'bloc_de_mise_en_avant') {
                  $accueil_id = current($item->get('field_ref_accueil')->getValue())['value'];

                  if(!empty($accueil_id)) {
                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
                    $query = $connection->query("select * from accueils where id_accueil='".$accueil_id."'");
                    while($bloc = $query->fetch()) {
                      var_dump($bloc->image);
                      $item->__set('field_image_save', $bloc->image);
                      $item->validate();
                      $item->save();
                    }
                  }
                }




                break;
              case "activite":
                $agenda = Node::load(152535);
                $agenda->__set('field_activite', 12);
                $agenda->validate();
                $agenda->save();

                $item->__unset('field_activite');
                $item->save();
                var_dump('ref activite : '.$item->get("field_ref_activite")->value);
                if($item->get("field_ref_activite")->value !== null) {
                  $activite = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                    'type' => 'activite',
                    'field_ref_activite' => $item->get("field_ref_activite")->value
                  ]));
                  
                  if($name === 'agenda' && !empty($activite)) {
                    var_dump("RECUPERATION DE L'ACTIVITE :".$activite->id());
                    var_dump("SAUVEGARDE DANS : ".$item->id());
                    $item->__set('field_activite', $activite->id());
                    //$item->validate();
                    $item->save();
                  }
                }
                
                break;
              /**
               * récupération des rubriques liées aux activités
               */
              case "rubriques":
                if($name === 'activite') {
                  $item->__unset("field_rubriques_activite");
                  $item->save();
                  $ref_act = current($item->get('field_ref_activite')->getValue())['value'];
                  if(!empty($ref_act)) {
                    var_dump($ref_act);
                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
                    $query = $connection->query("select * from asso_rubriques_activites where ref_activite = '".$ref_act."'");
                    while($asso=$query->fetch()) {
                      $rub=current(\Drupal::entityTypeManager()
                        ->getStorage('taxonomy_term')
                        ->loadByProperties(
                          [
                            "vid" => "rubrique_activites",
                            "field_ref_rubrique" => $asso->ref_rubrique
                          ]
                        ));
                      if(!empty($rub)) {
                        $item->__set('field_rubriques_activite', $rub);
                        $item->validate();
                        $item->save();
                        var_dump($rub->id());
                      }

                    }
                  }
                } else if($name==="article") {
                  
                }


                break;
              case 'repasse':
                //var_dump($item->get('field_adresse')->value);
                /**
                 * TRAITEMENT SUR DIFFERENTS CHAMPS A COMMENTER EN FOCNTION DE LA PRESENCE
                 */
                if($item->__isset('field_telephone')) {
                  if($item->get('field_telephone')->value === 'NULL') {
                    $item->set('field_telephone', null);
                  }
                }

                if($item->__isset('field_email')) {
                  if($item->get('field_email')->value === 'NULL') {
                    $item->set('field_email', null);
                  }
                }
                /*
                if($item->get('field_horaires')->value === 'NULL') {
                  $item->set('field_horaires', null);
                }*/
                if($item->__isset('field_lien')) {
                  if($item->get('field_lien')->value === 'NULL') {
                    $item->set('field_lien', null);
                  }
                }

                $allowed_tags = ['a', 'br'];
                if($item->__isset('body')) {
                  $item->__set('body', $this->propre(current($item->get('body')->getValue())['value']));
                  //$item->__set('body', html_entity_decode(current($item->get('body')->getValue())['value']));
                }
                if($item->__isset('field_resume')) {
                  $item->__set('field_resume', $this->propre($item->get('field_resume')->value));
                }
                if($item->__isset('field_coordonnees')) {
                  $item->__set('field_coordonnees', $this->propre($item->get('field_coordonnees')->value));
                  //$item->__set('field_coordonnees', str_replace("&#39;", "'", $item->get('field_coordonnees')->value));
                }
                if($item->__isset('field_info_complementaires')) {
                  $item->__set('field_info_complementaires', $this->propre($item->get('field_info_complementaires')->value));
                }
                if($item->__isset('field_adresse')) {
                  if($item->get('field_adresse')->value === 'NULL') {
                    $item->set('field_adresse', null);
                  }
                  $item->__set('field_adresse', $this->propre($item->get('field_adresse')->value));
                  //$item->__set('field_adresse', html_entity_decode($item->get('field_adresse')->value));
                }
                //
                if($item->__isset('field_horaires')) {
                  $item->__set('field_horaires', $this->propre(current($item->get('field_horaires')->getValue())['value']));
                }

                //$item->__set('field_lieu', html_entity_decode(current($item->get('field_lieu')->getValue())['value']));
                //$item->__set('field_horaires', html_entity_decode(current($item->get('field_horaires')->getValue())['value']));
                //

                var_dump('REPASSE .. OK');

                $item->validate();
                $item->save();

                break;
              case 'migrate':


                break;
              case 'adherent':

                $item->__unset("field_adherent");
                $item->save();
                if($name === 'client') {
                  if($item->get("field_ref_client")->value !== NULL && !empty($item->get("field_ref_client")->value)) {
                    var_dump("ref_client : ".$item->get("field_ref_client")->value);
                    $adherent = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                      'type' => 'adherent',
                      'field_ref_client' => $item->get("field_ref_client")->value
                    ]));
                    if (!empty($adherent)) {
                      $adherent->__set('title', str_replace("&#39;", "'", $adherent->getTitle()));
                      $adherent->save();
                      $item->__set('field_adherent', $adherent);
                      $item->validate();
                      $item->save();
                    }


                  }


                } else {

                  if (!empty($item->get("field_ref_adherent")->value)) {

                    $adherent = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                      'type' => 'adherent',
                      'field_ref_adherent' => (int)$item->get("field_ref_adherent")->value
                    ]));
                    if (!empty($adherent)) {
                      var_dump($adherent->id());
                      //$adherent->__set('title', str_replace("&#39;", "'", $adherent->getTitle()));
                      //$adherent->save();
                      $item->__set('field_adherent', $adherent);
                      $item->validate();
                      $item->save();
                    }

                  }
                }


                break;
              case 'ville':
                //$db= \Drupal::database();

                if (!empty($item) && $item->get("field_ref_ville")->value) {
                  // print($item->get("field_ref_ville")->value."-");

                  echo $item->get("field_ref_ville")->value . " ---- enregistrement";

                  $query = $this->connection->query("select * from villes where id_ville=\"" . (int)$item->get("field_ref_ville")->value . "\"");
                  $ville = current($query->fetchAll());

                  if ($ville) {
                    echo $ville->commune;
                    $item->__set("field_ville_save", $ville->commune);
                    $item->__set("field_code_postal", $ville->code_postal);
                    $item->validate();
                    $item->save();
                  }
                  echo "OK\n";
                  // print_r("select * from villes where id_ville='".$item->get("field_ref_ville")->value."'\n");


                }

                break;
              case "delcontact":
                echo "Suppression des contacts ... " . $item->id();
                $item->__unset("field_contact");
                $item->save();
                echo " ... OK\n";
                break;
              case "dept":
                if($name === 'bloc_de_mise_en_avant') {
                  $accueil_id = current($item->get('field_ref_accueil')->getValue())['value'];

                  if(!empty($accueil_id)) {
                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
                    $query = $connection->query("select * from accueils where id_accueil='".$accueil_id."'");
                    while($bloc = $query->fetch()) {
                      $term = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties(
                        [
                          "field_ref_dept" => $bloc->dept,
                          "vid" => "departement",
                        ]
                      );
                      if ($term) {
                        var_dump(current($term)->id());
                        $item->__set("field_departement", current($term));
                        $item->validate();
                        $item->save();
                        echo ".";
                      }
                    }
                  }
                } else {
                  $dept = (int)$item->get("field_ref_dept")->value;

                  if ($dept) {
                    echo "Traitement dept " . $dept . " ... ";

                    $term = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties(
                      [
                        "field_ref_dept" => $dept,
                        "vid" => "departement",
                        //"field_type_contact"=>"client"
                      ]
                    );

                    if ($term) {
                      $item->__set("field_departement", current($term)->id());
                      $item->validate();
                      $item->save();
                      echo ".";
                    }
                    echo "OK\n";
                  }
                }



                break;
              case "contact":
                if (!empty($item->get("field_ref_".$name)->value)) {
                  $item->__unset("field_contact");
                  $item->save();
                  echo "Traitement contacts du $name " . $item->id() . " ... ".$item->get("field_ref_" . $name)->value;
                  //echo "Chargement de la base contact clients ... ".$item->get("field_ref_" . $name)->value;
                  //echo $item->get("field_ref_client")->value;echo " \n";
                  $contacts = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
                    [
                      "type" => "contact",
                      "field_type_contact" => $name,
                      "field_ref_" . $name => $item->get("field_ref_" . $name)->value,
                    ]
                  ));
                  //var_dump($contacts->id());
                  if (!empty($contacts)) {
                    var_dump($contacts->id());
                    echo "Enregistrement des contacts ... ";
                    $item->get("field_contact")->appendItem($contacts);
                    $item->validate();
                    $item->save();
                    echo " OK\n";
                  }


                }


                break;
              case 'client':
                if (!empty($item->get("field_ref_client")->value)) {
                  var_dump($item->get("field_ref_client")->value);
                  $item->__unset('field_client');
                  $item->save();
                  //var_dump($item->get("field_ref_client")->value);
                  $client = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                    'type' => 'client',
                    'field_ref_client' => $item->get("field_ref_client")->value
                  ]));

                  $item->__set('field_client', $client->id());
                  $item->validate();
                  $item->save();
                  var_dump($client->id());
                }
                break;

            } // fin switch
          } // fin if option

        } // fin foreach options


      } // fin foreach rs
    }
  }

  /**
   * Echos back hello with the argument provided.
   *
   * @param string $name
   *   Argument provided to the drush command.
   *
   * @command kidiklik_migrate:adherent
   * @aliases kma
   * @options arr An option that takes multiple values.
   * @options msg Whether or not an extra message should be displayed to the user.
   * @usage drush9_example:hello akanksha --msg
   *   Display 'Hello Akanksha!' and a message.
   */
  public function adherent($name, $options = ['msg' => FALSE])
  {
    $rs = \Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
      [
        "type" => "contact",
        "field_type_contact" => "adherent"
      ]
    );
    
    if ($options['msg']) {
      $this->output()->writeln('Hello ' . $name . '! This is your first Drush 9 command.');
    } else {
      $this->output()->writeln('Hello ' . $name . '!');
    }
  }

  /**
   * Echos back hello with the argument provided.
   *
   * @param string $name
   *   Argument provided to the drush command.
   *
   * @command kidiklik_migrate:InscritsToMailjet
   * @aliases knl
   * @options arr An option that takes multiple values.
   * @options msg Whether or not an extra message should be displayed to the user.
   * @usage drush9_example:hello akanksha --msg
   *   Display 'Hello Akanksha!' and a message.
   */
  public function InscritsToMailjet($name, $options = ['msg' => FALSE])
  {
    echo \Drupal::request()->getBasePath();
    $url = 'http://' . \Drupal::request()->getHost() . \Drupal::request()->getBasePath() . '/' . drupal_get_path('module', 'kidiklik_front_newsletter') . '/email_to_newsletter.php';
    exec('wget ' . $url . '?email=test@freee.fr&dept=45');

    //echo file_get_contents($url.'?email=test@freee.fr&dept=45');
    echo $name;
  }
}
