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
    $desc=str_replace('&ecirc;','ê',$desc);
    $desc=str_replace('&ocirc;','ô',$desc);
    $desc=str_replace('&acirc;','â',$desc);
    $desc=str_replace('NULL','',$desc);
    $desc=str_replace('&agrave;','à',$desc);
    $desc=str_replace('&amp;','&',$desc);
    $desc=str_replace('<br>',chr(10),$desc);
    $desc=str_replace(['&#13;','&#10;'],['<br>','<br>'],$desc);
    //$desc=str_replace(['&lt;p&gt;','&lt;/p&gt;'],['<p>','</p>'],$desc);
    if($chariot===true) {
      $desc=str_replace(['&#13','&#10;'],['<br>','<br>'],$str);
    } else {
	    $desc=str_replace(['&#13;','&#10;'],['',''],$str);
    }
    $desc = str_replace(["&#39;","&#039;","&#034;","&#34;", '&#38;'], ["'","'",'"','"', '&'],$desc);
    //$desc = str_replace("&#34;", '"',$desc);
    //return html_entity_decode($str);
    //return htmlspecialchars_decode($desc);
    return ($desc);
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
    'article' => FALSE,
    'reportage' => FALSE,
    'date' => FALSE,
    'activite' => FALSE,
    'unpublished' => FALSE,
    'geo' => FALSE,
    'import' => FALSE,
    'paragraph' => FALSE,
    'content' => FALSE,
    'url'=>FALSE,
    'geolocation'=>FALSE,
    'client' => FALSE,
    'filtres' => FALSE,
    'migrate' => FALSE,
    'paragraphe' => FALSE,
    'delete' => FALSE,
    'rubrique_activite' => FALSE,
    'clients' => FALSE,
    'statut' => FALSE,
    'img_correct' => FALSE,
    'relation' => FALSE,
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

	    if($options['repasse'] === true) {
        $connection = \Drupal::database();
        $query= $connection->select('node','n');
                $query->fields('n',['nid']);
                $query->join("node__field_ref_accueil","ra","ra.entity_id=n.nid");
                //$query->leftJoin("paragraph__field_date_de_fin","fdd","fdd.entity_id=fd.field_date_target_id");
                $query->fields('ra',['entity_id', 'field_ref_accueil_value']);
                $query->condition('type','bloc_de_mise_en_avant','=');
                $rs=$query->execute();

        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
	while($item=$rs->fetch()) {
		if(!empty($item->field_ref_accueil_value)) {
			$query2 = $connection->query("select * from accueils where id_accueil=".$item->field_ref_accueil_value);
			$content = $query2->fetch();
			var_dump($content->titre);
			var_dump($item->nid);
			if($content->titre !== 'NULL' && !empty($item->nid) && $content->titre!==null) {
			$node = Node::load($item->nid);
			$node->__set('title',$this->propre($content->titre));
			$node->__set('field_resume',$this->propre($content->description));
			$node->save();
			}
		}
	}
      }else if($options['import'] === true) {
        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
        $query2 = $connection->query("select * from accueils where id_accueil>61201 order by id_accueil");
        
        while($item=$query2->fetch()) {
          $node = Node::create([
            'type' => 'bloc_de_mise_en_avant',
            'title' => $this->propre($item->titre),
            'field_resume' => $this->propre($item->description), 
            'field_lien'=> $item->url,
            'field_type' =>  $item->type,
            'field_ref_accueil' => $item->id_accueil,
            'field_ref_entite' => ($item->ref_entite==='reportage'?'article':$item->ref_entite),
            'field_ref_adherent' => $item->ref_adherent,
            'field_entite' => $item->entite,
            'field_image_save' => $item->image,
	    'uid' => 1,
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

                      $query3 = $connection->query("select * from accueils_dates where ref_accueil='".$item->id_accueil."'");
                      while($bloc = $query3->fetch()) {
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

      }else  if($options['date'] === true) {

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
                $query->condition('field_ref_accueil_value', 56955, '>');
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
	      ->condition('type','bloc_de_mise_en_avant','=');
	      //->condition('nid',186012,'='); // 84158
		
	      $rs=$query->execute();
	      Database::setActiveConnection('kidiklik');
	      while($item = $rs->fetch()) {
		$node = Node::Load($item->nid);
		
		$connection = \Drupal\Core\Database\Database::getConnection();
		if(!empty($node->get('field_ref_accueil')->value) && !empty($node)) {
			$query = $connection->query("select * from accueils where id_accueil = ".$node->get('field_ref_accueil')->value)->fetch();
			$titre = $this->propre($query->titre);
			$desc = $this->propre($query->description);
		  
			if($options['repasse'] === true) {
				if(!empty($titre)) {
					$node->set('title',$titre);
					$node->set('field_resume',$desc,true);

					$node->save();

				}
var_dump($node->id());
			var_dump($titre);
		  
		  } 

		    
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
	  //var_dump($n->get('body')->value);
	  $n->set('body', $this->propre($n->get('body')->value));
          //$n->set('field_type_reportage', false);
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
        $sql = "select * from ".$name." where date_fin >= '2019-01-01'";
        if($name == "editos") {
          //$sql.=" and id_edito > 6767";
        } else {
          //$sql.=" and id_test > 1012";
        }
        $query = $connection->query($sql);
       
	while($edito=$query->fetch()) {
          $dept=(int)$edito->dept;
	  if($dept>=22) $dept--;
	  if($dept<10) $dept='0'.$dept;

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
	    'uid' => 1,
	    'status' => $edito->active
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
            $node->__set('field_adherent', $adherent);
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
            /* $query2 = $connection->query("select nom from asso_rubriques_editos are join editos_rubriques er on er.id_rubrique = are.ref_rubrique where are.ref_edito =".$edito->id_edito);
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
              
	    }*/
         
          } else {
            //$node->__set('field_ref_reportage' , $edito->id_test);
          }


          if($name === 'editos') {
            $query3 = $connection->query("select * from editos_datas  where ref_edito =".$edito->id_edito.' order by ordre');
          } else {
            $query3 = $connection->query("select * from tests_datas  where ref_test =".$edito->id_test.' order by ordre');
	  }
	  $prem = 1;
	  while($para_edito = $query3->fetch()) {
            $desc=$this->propre($para_edito->description);
	    var_dump( 'https://www.kidiklik.fr/images/'.$name.'/'.$para_edito->image);
	    $file = null;
	    if(!empty($para_edito->image)) {
		    $data_para = file_get_contents('https://www.kidiklik.fr/images/'.$name.'/'.$para_edito->image);//$paragraph->get('field_image_save')->value);
	    
		    $file = file_save_data($data_para,\Drupal::config('system.file')->get('default_scheme').'://'.$para_edito->image);
									var_dump('record img paragraph');
	    }
	    //$paragraph->__set('field_image',['target_id' => $file->id()]);

	    if($prem === 1) {
		    $node->__set('field_image', !empty($file)?['target_id' => $file->id()]:null);
	
		    $node->__set('body', $para_edito->description);
		    $node->save();
		    $prem = 0;
		} else {	
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
		      'field_image'=> !empty($file)?['target_id' => $file->id()]:null,
		    ]);
	    		$paragraph->save();
		    $node->get('field_paragraphes')->appendItem($paragraph);
		}
          }

	  $node->save();
        
        }
      }
    }else if ($name == "adherents" || $name==='clients') { 
      Database::setActiveConnection('kidiklik');
      $connection = \Drupal\Core\Database\Database::getConnection();
      if($options['import'] === true) {
	      if($name === "adherents") {
		      $query = $connection->query("select * from adherents where id_adherent>7160 order by id_adherent desc");
		      $type = 'adherent';
	      } else {
		      $type = 'client';
		      $query = $connection->query("select * from clients order by id_client desc");
	      }
	      while($row = $query->fetch()) {
		      $test_node = null;
		      if($name === 'adherents') {
		      	$test_node = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
				'field_ref_adherent' => $row->id_adherent,
			       'type' => 'adherent',	
			]));
		      } else {
		      	$test_node = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
				'field_ref_client' => $row->id_client,
			       'type' => 'client',	
			]));
		      }
		      if(!empty($test_node)) {
			      var_dump($row->id_adherent ?? $row->id_client);
			      var_dump($test_node->id());
			      var_dump('adherent ou client existant');
			      continue;
		      }

          $query2 = $connection->query("select * from villes where id_ville = :ville", [
            ':ville' => $row->ref_ville
          ]);
          $row2=$query2->fetch();
          var_dump($row->nom);
          $dept=(int)$row->dept;
          if($dept>=22) $dept--;
  
	      if($name === "adherents") {
          $node=Node::Create([
            'type' => $type,
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

	  
	      } else {
          $node=Node::Create([
            'type' => $type,
            'title' => $this->propre($row->nom),
            'field_adresse' => $this->propre($row->adresse),
            'field_ref_ville' => $row->ref_ville,
            'field_email' => $this->propre($row->email),
            'field_lien' => $this->propre($row->url),
            'field_note' => $this->propre($row->note),
            'field_ref_dept' => $row->dept,
            'field_telephone' => $this->propre($row->telephone),
            'field_ref_client' => $row->ref_client,
            'field_ville_save' => $this->propre($row2->commune),
            'field_code_postal' => $this->propre($row2->code_postal),
    ]);
	      }

          $node->save();
          $term_dep=current(\Drupal::entityTypeManager()
              ->getStorage("taxonomy_term")
              ->loadByProperties(['vid' => 'departement','name'=>$dept]));
          if($term_dep !== false) {
            $node->__set('field_departement',$term_dep->id());
	  }
	  /*
          $contact = current(\Drupal::entityTypeManager()
          ->getStorage("node")->loadByProperties([
            'type' => 'contact',
            'field_ref_adherent' => $row->id_adherent
          ]));
          if(!empty($contact)) {
            var_dump("CONTACT: ".$contact->getTitle());
            $node->__get('field_contact')->appendItem($contact);
	  }*/

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
        $query = $connection->query("select * from ".$type."_contacts where id_contact > 3094");
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
        $query = $connection->query("select * from activites where id_activite > 5410 order by id_activite"); // where id_activite > 5240");
      } else {
	      $type='agenda';
        $query = $connection->query("select * from agendas   order by id_agenda"); // where id_agenda > 56803");
      }

     /* $result = \Drupal::entityQuery('node')
        ->condition('field_ref_parent', '670', '=')
        ->condition('vid', 'rubrique_activites')
        ->execute();*/

      while ($content = $query->fetch()) {
          $dept=(int)$content->dept;
	  if($dept>=22) $dept--;
        var_dump($content->titre);
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
		$type = 'activite';
            $tab = [
              'type' => 'activite',
              'title' => $this->propre($content->titre),
              'field_adresse' => $this->propre($content->adresse),
              'field_ref_ville' => $content->ref_ville,
              'field_duree'=> $content->duree,
              'field_ref_dept' => $content->dept,
              'field_resume' => $this->propre($content->resume),
              'body' => $this->propre($content->description),
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
	      'status' =>$content->active
            ];
            
          } else {
		$type = 'agenda';
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
              'field_video' => $content->video,
	      'status' =>$content->active
            ];
	  }
	  var_dump($type);
          $node= Node::Create($tab);
          $node->enforceIsNew();
          $node->save();
          var_dump($node->getTitle());
          //$node = node::Load($node->id());
	} //else {
var_dump('node id : '.$node->id());
	$save = 0;
	if($options['dept'] === true) {
          $term_dep=current(\Drupal::entityTypeManager()
              ->getStorage("taxonomy_term")
              ->loadByProperties(['vid' => 'departement','name'=>$dept]));
          if($term_dep !== false) {
		  $node->__set('field_departement',$term_dep->id());
		  $save = 1;
	  }
	}
          
       	if($options['ville'] === true) {
                  $query_ville = $this->connection->query("select * from villes where id_ville=\"" . (int)$content->ref_ville . "\"");
                  $ville = current($query_ville->fetchAll());

                  if (!empty($ville)) {
                    var_dump('ajout ville : '.$ville->commune);
                    $node->__set("field_ville_save", $ville->commune);
                    $node->__set("field_code_postal", $ville->code_postal);
                    $node->validate();
                    //$node->save();
		  $save = 1;
                  }
                  echo "OK\n";
	}	
          //if($node->id() > 45710)
	if($options['adherent'] === true) {
                    $adherent = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                      'type' => 'adherent',
                      'field_ref_adherent' => (int)$content->ref_adherent
                    ]));
                    if (!empty($adherent)) {
                      var_dump('ajout adherent : '.$adherent->id());
		      $node->__set('field_adherent', $adherent);
		  $save = 1;
		      //$node->save();
                    }
	}
	if($options['activite'] === true) {
                var_dump('ref activite : '.$node->get("field_ref_activite")->value);
                if($node->get("field_ref_activite")->value !== null) {
                  $activite = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                    'type' => 'activite',
                    'field_ref_activite' => $node->get("field_ref_activite")->value
                  ]));
                  
                  if($type === 'agenda' && !empty($activite)) {
                    var_dump("RECUPERATION DE L'ACTIVITE :".$activite->id());
                    var_dump("SAUVEGARDE DANS : ".$node->id());
                    $node->__set('field_activite', ['target_id' => $activite->id()]);
                    //$node->__set('field_activite', ['target_id' => 235129]);
		  $save = 1;
                    //$item->validate();
                    $node->save();
                  }
		}
	}
	if($save === 1) {
		$node->save();
	}
	if($options['rubriques'] === true) {
		$this->set_rubrique($node);
	}
	if($options['filtres'] === true) {
		$filtres = [];
		//Database::setActiveConnection('kidiklik');
		//$connection = \Drupal\Core\Database\Database::getConnection();
		$query_filtres = $connection->query("select * from filtres");

	        while($item = $query_filtres->fetch()) {
        	  $filtres[]=$item;
	        }
                if(!empty($node->get('field_ref_'.$type)->value)) {
                  $node->__unset('field_filtres');
                  $node->save();
                  $content_filtres= [];
                  foreach($filtres as $filtre) {
                    var_dump("select * from entite_filtre_".$filtre->identifiant."_valeur where entite='".$type."' and ref_entite=".$node->get('field_ref_'.$type)->value);
                    $query_entite_filtre = $connection->query("select * from entite_filtre_".$filtre->identifiant."_valeur where entite='".$type."' and ref_entite=".$node->get('field_ref_'.$type)->value);
                    while($kdk_filtre = $query_entite_filtre->fetch()) {
                      $content_filtres[$kdk_filtre->identifiant][] = $kdk_filtre->valeur;

                    }
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
                    $node->get('field_filtres')->appendItem($filtre);
                      $node->validate();
                      $node->save();
                  }
                }
	}

          // var_dump(current($node->get('field_departement')->getValue())['target_id']);
            if ($options['images'] === true) {
              if ($name === 'kidi_activites') {
                $query2 = $connection->query("select * from activites_galeries where ref_activite = '" . $content->id_activite . "'");
              } else {
                $query2 = $connection->query("select * from agendas_galeries where ref_agenda = '" . $content->id_agenda . "'");
	      }
	      $node->__unset('field_image');
	      $node->save();

	      while ($image = $query2->fetch()) {
		      if((fopen('https://www.kidiklik.fr/images/'.$type.'s/'.$image->image,'r')==true)) {
			      $data = file_get_contents('https://www.kidiklik.fr/images/'.$type.'s/'.$image->image);
		      }
				if(!empty($data)) {

					var_dump('insert image : ' . $image->image);
					$file = file_save_data($data,\Drupal::config('system.file')->get('default_scheme').'://'.$image->image);
					if(!empty($file) && (int)$file->get('filesize')->value !== 13541) {
						var_dump('record img');
						var_dump($file->id());
						$node->__set('field_image', ['target_id' => $file->id()]);
						$node->save();
					}
				} else {

					var_dump('insert image save: ' . $image->image);

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
                      var_dump($agenda_date->date_debut);
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

    } else if($name === 'kidi_publicite') { 
	    if($options['delete']===true) {
		    $pa = \Drupal::entityTypeManager()->getStorage("node")->loadByProperties(['type'=>'publicite']);
		    foreach($pa as $node) {
			    var_dump($node->id());
			    $node->delete();
		    }

	    } else { 
        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
	$query = $connection->query('select * from publicites where id_publicite>8369 order by id_publicite asc');
	while($item=$query->fetch()) {
          	$dept=(int)$item->dept;
		if($dept>=22) $dept--;
                $term_dept = get_term_departement($dept);
		var_dump($item->to_dept);
		$map = [
			"1" => 95,
			"2" => 97,
			"3" => 98,
			"5" => 957,
		       "6" => 106	
	       ];
		if(!empty($item->titre)) {
			$tousite=0;
			if($item->to_dept === "0") {
				$tousite=1;
			}
			$node=Node::Create([
			'type' => 'publicite',
			'title' => $this->propre($item->titre),
			'field_format' => $map[$item->format],
			'field_date_debut' => $item->date_debut,
			'field_date_fin' => $item->date_fin,
			'field_ref_adherent' => $item->ref_adherent,
			'field_ref_publicite' => $item->id_publicite,
			'field_url' => $item->url,
			'field_script' => $item->script,
			'field_image_save' => $item->image,
			'field_departement' => $term_dept,
			'field_tous_les_sites' => $tousite,
			'status' => $item->active
			]);

			$node->save();
			if($tousite === 1) {
                            $query2 = \Drupal::entityQuery('taxonomy_term')
                            ->condition('status', '1', '=')
                            ->condition('vid', 'departement');
                            $depts = Term::loadMultiple($query2->execute());
                            foreach($depts as $dept) {
                              $node->__get('field_partage_departements')->appendItem([
                                'target_id' => $dept->id()
                              ]);
			    }
			    
			}
			
		var_dump($node->get('field_date_debut')->getValue());
		}
	}
	    }


    }  else if($name==='nettoyage_rubriques') {

          $rubriques = \Drupal::entityTypeManager()
		  ->getStorage('taxonomy_term')
		  ->loadByProperties([
			  'name' => "Au bord de l'eau"
		  ]);
	  foreach($rubriques as $rubrique) {
		  var_dump($rubrique->id());
		  $rubrique->delete();
	  }
	    

    }  else if ($name === 'paragraph') {

    	
	    if($options['delete'] === true) {
		    if($options['paragraphe'] === true) {
			    $type = 'paragraphe';
		    } else if($options['date'] === true) {
			    $type = 'date';
		    }

		/*    $liste = \Drupal::entityTypeManager()->getStorage("paragraph")->loadByProperties(
			    [
				    'type' => 'paragraphe'
			    ]
		    );*/
		$connection = \Drupal::database();
		$query= $connection->select('paragraphs_item','p');
                $query->fields('p',['id', 'type']);
                $query->condition('type',$type,'=');
                $rs=$query->execute();
		while($item=$rs->fetch()) {
			$pa = \Drupal::entityTypeManager()->getStorage("paragraph")->load($item->id);
			if(!empty($pa->id)) {

				var_dump(current($pa->get('field_titre')->getValue())['value']);
				$pa->delete();
			}
		}

	    }

    }else if ($name === 'format') {
        $connection = \Drupal::database();
        $rs = $connection->query('update paragraph__field_description set field_description_format = "basic_html"');
       	$rs->execute(); 
        $rs = $connection->query('update node__body set body_format = "basic_html"');
       	$rs->execute(); 
        //$rs = $connection->query('update node__field_resume set field_resume_value = "basic_html"');

    }else if ($name==='files') {
			//$f=\Drupal::entityTypeManager()->getStorage('file')->load(280);			$f->delete();exit;

    }else if ($name==='kidi_jeu_concours') {
        Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
	$query = $connection->query("select c.*, cd.image as img, cd.description,cd.titre as sous_titre from concours c join concours_datas cd on c.id_concours=cd.ref_concours  order by id_concours");
	while($item = $query->fetch()) {
		if($options['repasse'] === true) {
			$node=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([

			   ]);


		} else {
			$file = null;
			if(!empty($item->img)) {

				$data = file_get_contents('https://www.kidiklik.fr/images/concours/'.$item->img);
				if(!empty($data)) {

					$file = file_save_data($data,\Drupal::config('system.file')->get('default_scheme').'://'.$item->img);
				}
			}

          	$dept=(int)$item->dept;
		if($dept>=22) $dept--;
                $term_dept = get_term_departement($dept);
		var_dump($item->titre);
		$node=Node::Create([
			'type' => 'jeu_concours',
			'title' => $this->propre($item->titre),
			'field_sous_titre' => $this->propre($item->sous_titre),
			'body' => $item->description,
			'field_resume' => $this->propre($item->resume),
			'field_date_debut' => $item->date_debut,
			'field_date_fin' => $item->date_fin,
			'field_ref_adherent' => $item->ref_adherent,
			'field_image_save' => $item->img,
			//'field_image' => ['target_id' => $file->id()],
			'status'=>$item->active,
			'field_departement' => $term_dept,
			'field_ref_jeu_concours' => $item->id_concours,
			'status' => $item->active
		]);
		$node->save();
		if(!empty($file)) {
			$node->__set('field_image', ['target_id' => $file->id()]);
			$node->save();
		}
		var_dump($node->id());
		}
	}

    }else if ($name) {

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
                //$query->condition('nid',264416,'<=');
                //$query->condition('field_date_de_debut_value','2019-01-01','<'); /* pour contenu article etc */
		//$query->condition('field_date_de_fin_value','2020-01-01','<');
		$query->orderBy('nid','desc');
                $rs=$query->execute();
		while($item=$rs->fetch()) {
			
			var_dump($item->nid);
			$node=Node::load($item->nid);

			if(!empty($node)) {
			/*	$id_date = $node->get('field_date')->getValue();
				if(!empty($id_date)) {
					foreach($id_date as $id) {
						$pa = \Drupal::entityTypeManager()->getStorage("paragraph")->load($id['target_id']);
						var_dump("delete date : ".$id['target_id']);
						$pa->delete();
					}
				}
			 */
				var_dump($node->getTitle() .'...DELETED');

				$node->delete();

                    
			}

                 // 
                  //var_dump($node->get('field_date')->getValue());
                  

                }
                
      } else {
        $connection = \Drupal::database();
        $rs = $connection->query('select * from node where type=:type order by nid desc', [
          ':type' => $name,
        ], [
          'fetch' => 'node'
  ]);
	if($name === 'paragraphe') {
		$rs = $connection->query('select * from paragraphs_item where type="paragraphe" order by id desc', [
			':type' => $name,
		]);
	}
        
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
        Database::setActiveConnection('kidiklik');
        $connection_kidi = \Drupal\Core\Database\Database::getConnection();

      while ($result = $rs->fetchObject()) {
        $nid = $result->nid;

	if($name==='paragraphe') {
		$item = \Drupal\paragraphs\Entity\Paragraph::load($result->id);
	}else {
		$item = Node::Load($nid);
	}
	/*$val=$item->get('field_date')->getValue()[1]['target_id'];
	$d = \Drupal\paragraphs\Entity\Paragraph::load($val);
	if(!empty($d)) {
	var_dump($d->get('field_date_de_fin')->getValue()[0]['value']);
	if($d->get('field_date_de_fin')->getValue()[0]['value'] < '2020-01-01') {
		//$item->delete();
		continue;
	}
	}*/
        var_dump("Chargement  '$name : $result->nid' ...");
        /*Database::setActiveConnection('kidiklik');
        $connection = \Drupal\Core\Database\Database::getConnection();
        $query = $connection->select('agendas','a')
        ->fields('a',['resume', 'description'])
        ->condition('a.id_agenda',$item->get('field_ref_agenda')->value, '=')
        ->execute()
        ->fetch();*/
        //$item->__set('title', $this->propre($item->getTitle()));

        //$item->__set('body', $this->propre($item->get('body')->value));
        //$item->validate();
        //$item->save();
        //var_dump($query);

        // var_dump($item);
        foreach ($options as $key => $option) {

          if ($option) {

		  switch ($key) {
		  case 'paragraph':
                	Database::setActiveConnection('kidiklik');
	                $connection = \Drupal\Core\Database\Database::getConnection();
			$id_entity = $item->get('field_ref_entite')->value;
			if(!empty($id_entity)) {
				if((int)$item->get('field_type_reportage')->value === 0) {

                		$query = $connection->query('select * from editos_datas where ref_edito='.$id_entity);
					while($rs_para=$query->fetch()) {
				var_dump($rs_para);
					}
				exit;


			}
			}


			  break;
		  case 'relation':
			  $entite=null;
			  if(!empty($item->get('field_entite')->value) && !empty($item->get('field_ref_entite')->value)) {

				  switch($item->get('field_entite')->value) {
				  case 'reportage':
				  case 'article':
					  $entite=\Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
						  'type' => 'article',
						  'field_ref_entite' => $item->get('field_ref_entite')->value
					  ]);

					  break;
				  case 'activite':
					  $entite=\Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
						  'type' => 'activite',
						  'field_ref_activite' => $item->get('field_ref_entite')->value
					  ]);

					  break;
				  case 'agenda':
					  $entite=\Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
						  'type' => 'agenda',
						  'field_ref_agenda' => $item->get('field_ref_entite')->value
					  ]);

					  break;

				  
				  }
				  if(!empty($entite)) {
					  current($entite)->__unset('field_mise_en_avant');
					  current($entite)->get('field_mise_en_avant')->appendItem($item);
					  current($entite)->save();
					  var_dump(current($entite)->getTitle());
				  }
			  }
			  break;

		  case 'img_correct':
			  /*$f=\Drupal::entityTypeManager()->getStorage('file')->load(1073);			
			  var_dump($f->get('filesize')->value);
			  exit;*/
			  $liste = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(
      [
	      'type' => 'agenda',
	      'nid' => '284259',
//        'field_activite' =>236616 
      ]
    );
			  var_dump(current($liste->get('field_activitt')));
			  exit;
			  $i=current($item->get('field_image')->getValue());
			  if($item->__isset('field_image')===true) {
				  var_dump('delete image save');
				$item->__unset('field_image_save');
				$item->save();
			  }
			  break;
		case 'images': 
			//$f=\Drupal::entityTypeManager()->getStorage('file')->load(280);			$f->delete();exit;

			$img = current($item->get('field_image_save')->getValue())['value'];
			var_dump($img);
			if(empty($img)) {
                		/*Database::setActiveConnection('kidiklik');
				$connection = \Drupal\Core\Database\Database::getConnection();
				switch($name) {
				case 'agenda':

					break;
				}*/

			}

			$data=null;
			if(!empty($img)) {
				if($name ==='paragraphe'){

				} elseif($name === 'article') {
					$rep='editos';
					if((int)$item->get('field_type_reportage')->value !== 0) {
						$rep='tests';
					}
					if($options['paragraphe']===true) {
						$paras=$item->get('field_paragraphes')->getValue();
						if(!empty($paras)) {
							foreach($paras as $para) {
								$paragraph = \Drupal\paragraphs\Entity\Paragraph::load($para['target_id']);
								$img=$paragraph->get('field_image_save')->value;
								if(!empty($img)) {
									$data_para = file_get_contents('https://www.kidiklik.fr/images/'.$rep.'/'.$img);//$paragraph->get('field_image_save')->value);
									$file = file_save_data($data_para,\Drupal::config('system.file')->get('default_scheme').'://'.$img);
									var_dump('record img paragraph');
									$paragraph->__set('field_image',['target_id' => $file->id()]);
									$paragraph->__unset('field_image_save');
									$paragraph->save();

									var_dump($paragraph->get('field_image_save')->value);
								}


							}
						}
					}
						$ref=$item->get('field_ref_entite')->value;
						var_dump($ref);
						$query = $connection_kidi->query('select * from editos_datas where ref_edito='.$ref);
						$rsimg=$query->fetch();
						$img = $rsimg->image;
							var_dump((int)$item->get('field_type_reportage')->value);

						$data = file_get_contents('https://www.kidiklik.fr/images/'.$rep.'/'.$img);

					
				} elseif($name ==='activite') {
					if((fopen('https://www.kidiklik.fr/images/activites/'.$img,'r')==true)) {
						$data = file_get_contents('https://www.kidiklik.fr/images/activites/'.$img);
					}
				} elseif($name ==='agenda') {

					if((fopen('https://www.kidiklik.fr/images/agendas/'.$img,'r')==true)) {
						$data = file_get_contents('https://www.kidiklik.fr/images/agendas/'.$img);
					}
				} elseif($name ==='bloc_de_mise_en_avant') {
					if((fopen('https://www.kidiklik.fr/images/accueil/'.$img,'r')==true)) {
						$data = file_get_contents('https://www.kidiklik.fr/images/accueil/'.$img);
					}
				} elseif($name ==='publicite') {
					if((fopen('https://www.kidiklik.fr/images/vendos/'.$img,'r')==true)) {
						$data = file_get_contents('https://www.kidiklik.fr/images/vendos/'.$img);
					}
				} elseif($name ==='jeu_concours') {
					if((fopen('https://www.kidiklik.fr/images/concours/'.$img,'r')==true)) {
						$data = file_get_contents('https://www.kidiklik.fr/images/concours/'.$img);
					}
				}

				if(!empty($data)) {

					$file = file_save_data($data,\Drupal::config('system.file')->get('default_scheme').'://'.$img);


					if(!empty($file) && (int)$file->get('filesize')->value !== 13541) {
					var_dump($file->get('filesize')->value);
						var_dump('record img');
						var_dump($file->id());
						$item->__set('field_image', ['target_id' => $file->id()]);
						$item->__unset('field_image_save');
						$item->save();
					}
				}
			}
			break;

              case 'partage':
                Database::setActiveConnection('kidiklik');
                $connection = \Drupal\Core\Database\Database::getConnection();
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
		  case 'bloc_de_mise_en_avant':
                      $id_entity = $item->get('field_ref_accueil')->value;

			  break;
                }  
                $query = $connection->select('partages','a')
                    ->fields('a',['ref_id', 'dept', 'to_dept'])
                    ->condition('a.ref_id', $id_entity, '=')
		    ->execute()->fetch();
		var_dump($query);
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
				    var_dump('partage avec : '.$to_dept);
                              $dept = Term::load($term_dept);
				    $item->__unset('field_partage_departements');

				    $item->save();

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
	      case 'status':

		      if(!empty($item->get('field_ref_'.$name)->value)) {
                	Database::setActiveConnection('kidiklik');
			$connection = \Drupal\Core\Database\Database::getConnection();
			$query = $connection->query('select * from '.$type.'s where id_'.$type.'='.$item->get('field_ref_'.$name)->value);
			var_dump($query->fetch());
		      }
			break;
              case 'filtres':
                Database::setActiveConnection('kidiklik');
                $connection = \Drupal\Core\Database\Database::getConnection();
               //var_dump('ref entite : '.$item->get('field_ref_'.$name)->value);
                if(!empty($item->get('field_ref_'.$name)->value)) {
                  $item->__unset('field_filtres');
                  $item->save();
                  $content_filtres= [];
                  foreach($filtres as $filtre) {
                    //var_dump("select * from entite_filtre_".$filtre->identifiant."_valeur where entite='".$name."' and ref_entite=".$item->get('field_ref_'.$name)->value);
                    $query = $connection->query("select * from entite_filtre_".$filtre->identifiant."_valeur where entite='".$name."' and ref_entite=".$item->get('field_ref_'.$name)->value);
                    while($content = $query->fetch()) {
                      $content_filtres[$filtre->identifiant][] = $content->valeur;

                    }

                    /**/
		  }
                  if(!empty($content_filtres)) {
			var_dump('ajout des filtres ...');
                    $drupal_filtre = \Drupal\paragraphs\Entity\Paragraph::create([
                      'type' => 'filtres',
                      'field_envies' => [
                        'value' => (isset($content_filtres['envie'])?($content_filtres['envie'][0]):null)
                      ],
                      'field_thematiques' => [
                        'value' => (isset($content_filtres['thematique'])?($content_filtres['thematique'][0]):null)
                      ],
                    ]);
                    $drupal_filtre->save();
		    
		    foreach($content_filtres['vacances'] as $val) {
                      $drupal_filtre->get('field_vacances')->appendItem(['value'=>ucfirst($val)]);
                    }
                    foreach($content_filtres['tranches_age'] as $val) {
                     // var_dump($val);
			    preg_match("/([0-9]*)-([0-9]*)ans/",$val,$match);
                      if(!empty($match)) {
                     //   $val=$match[1]."-".$match[2]."ans";
                        //$filtre->get('field_tranches_d_ages')->appendItem($val);
                      }
                      $drupal_filtre->get('field_tranches_d_ages')->appendItem($val);

		    }
                    $drupal_filtre->validate();
                    $drupal_filtre->save();
                    $item->get('field_filtres')->appendItem($drupal_filtre);
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
                    $id_entite = $item->get('field_ref_entite')->value;
                    $lien = current($item->get('field_lien')->getValue())['value'];
                    preg_match('/([https:\/\/])([0-9]{2}|[a-z]*).kidiklik.fr\/(.*)\/([0-9]*)-(.*).html/',$lien,$match);
                    var_dump($match);
                  var_dump($id_entite); 
		    if(!empty($id_entite) && count($match)) {
			    var_dump($match[3]);
		     	if($match[3] === 'articles') {

                	      $node=current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                        	'type' => 'article',
	                       'field_ref_entite' => $id_entite
				]));
			} else if($match[3]==='sorties-moment'){
                	      $node=current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                        	'type' => 'agenda',
	                       'field_ref_agenda' => $id_entite
		       ]));

			} else {
                	      $node=current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                        	'type' => 'activite',
	                       'field_ref_activite' => $id_entite
				]));
			}
			if(!empty($node)) {
				$item->__unset('field_lien');
				$item->save();
                        var_dump("id node : ".$node->id());
                        var_dump(\Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$node->Id()));
                        $new_link=\Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$node->Id());
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
		} else if($name === 'article') {
			$p_id = current($item->get('field_paragraphes')->getValue())['target_id'];
			$p = $item->get('field_paragraphes')->getValue();
			foreach($p as $pp) {
				var_dump($pp['target_id']);
				$p_id=$pp['target_id'];
				$img=current(\Drupal::entityTypeManager()->getStorage("paragraph")->load((int)$p_id)->get('field_image_save')->getValue())['value'];
				if(!empty($img)) {
					break;
				}
			}

					var_dump($img);
			if(!empty($img)) {

					$item->set('field_image_save', $img);
					$item->save();
			}
		}




                break;
              case "activite":
                /*$item->__unset('field_activite');
		$item->save();*/
                var_dump('ref activite : '.$item->get("field_ref_activite")->value);
                if($item->get("field_ref_activite")->value !== null) {
                  $activite = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                    'type' => 'activite',
                    'field_ref_activite' => $item->get("field_ref_activite")->value
                  ]));
                  
                  if($name === 'agenda' && !empty($activite)) {
                    var_dump("RECUPERATION DE L'ACTIVITE :".$activite->id());
                    var_dump("SAUVEGARDE DANS : ".$item->id());
                    $item->__set('field_activite_lie',  $activite);
                    $item->validate();
		    $item->save();
		    //$connection->query("insert into node__field_activite values ('agenda',0,".$item->id().",0,'fr',0,".$activite->id().")");
                  }
                }
                
                break;
              /**
               * récupération des rubriques liées aux activités
               */
              case "rubriques":
		      if($name === 'activite') {
			      $this->set_rubrique($item);
                  /*$item->__unset("field_rubriques_activite");
                  $item->save();
                  $ref_act = current($item->get('field_ref_activite')->getValue())['value'];
                  if(!empty($ref_act)) {
                    var_dump($ref_act);
                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
                    $query = $connection->query("select * from asso_rubriques_activites where ref_activite = '".$ref_act."'");
		    while($asso=$query->fetch()) {
			    var_dump("rub id kidi : ".$asso->ref_rubrique);
                      $rub=current(\Drupal::entityTypeManager()
                        ->getStorage('taxonomy_term')
                        ->loadByProperties(
                          [
                            "vid" => "rubriques_activite",
                            "field_ref_rubrique" => $asso->ref_rubrique
                          ]
		  ));
			    if(!empty($rub)) {

				var_dump($rub->getName());	    
                        $item->__set('field_rubriques_activite', $rub);
                        $item->validate();
                        $item->save();
                        var_dump($rub->id());
                      }

                    }
		  }*/
		}

		      break;
	      case 'repasse':
                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
		      if($name === 'jeu_concours') {
		    $query = $connection->query("select * from concours where id_concours = '".$item->get('field_ref_jeu_concours')->value."'");
		    $rs_desc=$query->fetch();
		    $item->__set('field_resume',$this->propre($rs_desc->resume));
		    $item->save();

		      } else if($name === 'activite') {
		    $query = $connection->query("select * from activites where id_activite = '".$item->get('field_ref_activite')->value."'");
		    $rs_desc=$query->fetch();
		    $item->__set('field_resume',$this->propre($rs_desc->resume));
		    $item->save();

		      } else if($name === 'agenda') {
		    $query = $connection->query("select * from agendas where id_agenda = '".$item->get('field_ref_agenda')->value."'");
		    $rs_desc=$query->fetch();
		    $item->__set('field_resume',$this->propre($rs_desc->resume));
		    $item->save();

		      } else if($name === 'bloc_de_mise_en_avant') {
		    $query = $connection->query("select * from accueils where id_accueil = '".$item->get('field_ref_accueil')->value."'");
		    $rs_desc=$query->fetch();
		    var_dump($rs_desc->description);
		    $item->__set('field_resume',$this->propre($rs_desc->description));
		    $item->save();

		      } else {
                //var_dump($item->get('field_adresse')->value);
                /**
                 * TRAITEMENT SUR DIFFERENTS CHAMPS A COMMENTER EN FOCNTION DE LA PRESENCE
                 */
                if($item->__isset('field_telephone')) {
                  if($item->get('field_telephone')->value === 'NULL') {
                    $item->set('field_telephone', null);
		  }else {
			  $item->set('field_telephone', $this->propre($item->get('field_telephone')->value));

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
                  $item->__set('field_resume', $this->propre(str_replace('<br>',chr(10),$item->get('field_resume')->value)));
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
		      }
                break;
	      case 'statut':
                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
		      switch($name) {
		      case 'agenda':
			      if(!empty($item->get('field_ref_agenda')->value)) {
				      $query = $connection->query('select * from agendas where id_agenda ='.$item->get('field_ref_agenda')->value);
				      $rs_statut=$query->fetch();
				      if(!empty($rs_statut)) {
					      $item->__set('status',$rs_statut->active);
					      $item->save();
				      }

			      }

			      break;
		      case 'bloc_de_mise_en_avant':
			      if(!empty($item->get('field_ref_accueil')->value)) {
				      $query = $connection->query('select * from accueils where id_accueil ='.$item->get('field_ref_accueil')->value);
				      $rs_statut=$query->fetch();
				      if(!empty($rs_statut)) {
					      var_dump('status : '.$rs_statut->active);
					      $item->__set('status',$rs_statut->active);
					      $item->save();
				      }
			      }

			      break;
		      }


                break;
              case 'adherent':

                if($name === 'client') {
                	$item->__unset("field_adherent");
                	$item->save();
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
			if(count($item->__get('field_adherent')->getValue())) {
				continue;
			}
                  if (!empty($item->get("field_ref_adherent")->value)) {
			  $item->__unset('field_adherent');
			  $item->save();

                    $adherent = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                      'type' => 'adherent',
                      'field_ref_adherent' => (int)$item->get("field_ref_adherent")->value
                    ]));
                    if (!empty($adherent)) {
                      var_dump($adherent->getTitle());
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
			    var_dump('dep : '.$bloc->dept);
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
		} elseif($name === 'article') {
			if(!count($item->get('field_departement')->getValue())) {
/*                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
				if($item->get('field_type_reportage')->value === 0) {

					$query = $connection->query("select * from editos where id_edito='".$item->get('field_ref_entite')->value."'");
				} else {
					$query = $connection->query("select * from tests where id_test='".$item->get('field_ref_entite')->value."'");
				}
$tmp = $query->fetch();*/
				var_dump($item->get('field_departement')->getValue());
					$item->__set('field_departement', ['target_id'=>63]);
					$item->save();
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


	public function set_rubrique($item) {
                  $item->__unset("field_rubriques_activite");
                  $item->save();
                  $ref_act = current($item->get('field_ref_activite')->getValue())['value'];
                  if(!empty($ref_act)) {
                    var_dump($ref_act);
                    Database::setActiveConnection('kidiklik');
                    $connection = \Drupal\Core\Database\Database::getConnection();
                    $query = $connection->query("select * from asso_rubriques_activites where ref_activite = '".$ref_act."'");
		    while($asso=$query->fetch()) {
			    var_dump("rub id kidi : ".$asso->ref_rubrique);
                      $rub=current(\Drupal::entityTypeManager()
                        ->getStorage('taxonomy_term')
                        ->loadByProperties(
                          [
                            "vid" => "rubriques_activite",
                            "field_ref_rubrique" => $asso->ref_rubrique
                          ]
		  ));
			    if(!empty($rub)) {

				var_dump($rub->getName());	    
                        $item->__set('field_rubriques_activite', $rub);
                        $item->validate();
                        $item->save();
                        var_dump($rub->id());
                      }

                    }
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
