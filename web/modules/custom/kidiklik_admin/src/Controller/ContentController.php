<?php

namespace Drupal\kidiklik_admin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\views\Views;
use Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;
use Drupal\paragraphs\Entity\Paragraph;
/**
 * Class ContentController.
 */
class ContentController extends ControllerBase
{

  /**
   * Redirect.
   *
   * @return string
   *   Return Hello string.
   */
  public function pages()
  {
    $dep = get_term_departement();
    $response = new RedirectResponse("/taxonomy/term/" . $dep . "/edit");
    return $response;
  }

  /**
   * lance une action sur un noeud en ajax
   * @return Ajax response
   *
   */
  public function action($action, $node_id)
  {

    $node = Node::load($node_id);
    if ($action == "status") {
      if ($node->get("status")->value) {
        $node->set("status", false);
        $css = ["background" => "red"];
      } else {
        $node->set("status", true);
        $css = ["background" => "green"];
      }
    }

    $node->save();

    $response = new AjaxResponse();
    $Selector = '.node-status-' . $node_id;


    $response->addCommand(new CssCommand($Selector, $css));

    return $response;
  }

  public function getVilles($cp)
  {
    $database = \Drupal::database();
    $query = $database->query("select * from villes where code_postal='" . $cp . "'");
    $villes = $query->fetchAll();
    $tab = [];
    foreach ($villes as $ville) {
      $tab[] = ["name" => $ville->commune, "tid" => $ville->id_ville];
    }
    //kint(json_encode($tab));
    return new JsonResponse(($tab));
  }

  public function manageContent() {
      
      $offset = filter_input(INPUT_GET, 'offset') ?? 0;
      $racine = "/KIDIKLIK/WEBSITE/preprod.kidiklik.fr/tmp/";
      
      /*$file=fopen("/KIDIKLIK/WEBSITE/preprod.kidiklik.fr/tmp/agenda-manquant-final.csv","r");
      $ids=json_decode(file_get_contents($racine."agenda-manquant-id.json"),true);
      
      if($offset === 551) exit;
      $id=$ids[$offset];

    
      try {
          echo $id;
          
          $data = json_decode(file_get_contents($racine."JSON/node-".$id.".json"), true);
    
          $data_dates = json_decode(file_get_contents($racine."JSON/node-".$id."-date.json"), true);
    
          $data_filtres = json_decode(file_get_contents($racine."JSON/node-".$id."-filtres.json"), true);
    
          unset($data['field_date']);
    

              
              $node = Node::create($data);
              $dates = [];
              $paragraphs = [];

              foreach($data_dates as $d) {
                  $p = Paragraph::create([
                      'type' => 'date',
                      'field_date_de_debut' => $d['deb'],
                      'field_date_de_fin' => $d['fin'],
                  ]);

                  $p->save();

                  $paragraphs[] = [
                      'target_id' => $p->id(),
                      'target_revision_id' => $p->getRevisionId(),
                  ];
              }
              $node->set('field_date', $paragraphs);
              
              $paragraphs_filtres = [];
              foreach($data_filtres as $d) {
                  $val = [
                      'type' => 'filtres',
                  ];
                  if($d['theme'] !== null) {
                      $val['field_thematiques'] = $d['theme'];
                  }
                  if($d['age'] !== null) {
                      $val['field_tranches_d_ages'] = $d['age'];
                  }
                  if($d['vac'] !== null) {
                      $val['field_vacances'] = $d['vac'];
                  }
                  $p = Paragraph::create($val);

                  $p->save();

                    $node->field_filtres= [
                      'target_id' => $p->id(),
                      'target_revision_id' => $p->getRevisionId(),
                  ];
              }

              //$dates=
              $node->save();
              
              $node->field_departement = $data['field_departement'];
              $node->save();
      } catch(Exception $e) {

                  kint($e);
      }

    echo "<script>
            setTimeout(() => {
                document.location.href='?offset=".($offset+1)."'
            }, 1000)
            </script>
            ";
            exit;*/

     //Database::setActiveConnection('prod_du_11');
     //$database = Database::getConnection('default','prod_du_11');

     //$file=fopen("/KIDIKLIK/WEBSITE/preprod.kidiklik.fr/tmp/liste-agenda-id.json","r");
//      $ids=json_decode(file_get_contents($racine."liste-agenda-id.json"),true);
      
      $ids=json_decode(file_get_contents($racine."liste-id-retest.json"),true);
      
      /*$database = \Drupal::database();
      $query = $database->select('node_field_data', 'n');
      $query->fields('n', ['nid']);
      $query->condition('n.type', 'agenda');
      $query->condition('n.nid', $ids, 'NOT IN');
      $query->join('node__field_date', 'nb', 'nb.entity_id = n.nid');
      $query->join('paragraph__field_date_de_fin', 'p', 'p.entity_id = nb.field_date_target_id');
      $query->addField('p', 'field_date_de_fin_value', 'date_fin');
      $query->condition('p.field_date_de_fin_value', '2022-01-01', '<');
      $query->groupBy('n.nid');
      $query->orderBy('p.field_date_de_fin_value');
      $query->range($offset,100);
      $result = $query->execute()->fetchAll();
      $new_ids=[];
      $urls = [];
      $not_found=[];
      foreach($result as $r) {
          $node=Node::load($r->nid);
                  //echo sprintf("%s - %s <br>", $node->id(),$node->getType());
                  foreach ($node->get('field_date')->referencedEntities() as $paragraph) {
                      $fin = $paragraph->get('field_date_de_fin')->value;
                      $deb = $paragraph->get('field_date_de_debut')->value;
                      $dates=[];
                      if((!str_contains($fin,"2017") && !str_contains($fin,"2018") && !str_contains($fin, "2019") && !str_contains($fin,"2020") && !str_contains($fin,"2021"))){
$dates[]=$fin;
                          echo sprintf("->%s - %s - %s<br>",$node->id(),$deb,$fin);
                      }
                      if(!empty($dates)) {

                          $new_ids[]=$node->id();
                      }
                  }
      }
      $les_ids=json_decode(file_get_contents($racine."agenda-recup.json"), true);
      if($les_ids !== null) {
        $new_ids=array_merge($new_ids,$les_ids);
      }
      file_put_contents($racine."agenda-recup.json", json_encode($new_ids));
    echo "<script>
            setTimeout(() => {
                document.location.href='?offset=".($offset+100)."'
            }, 1000)
            </script>";
            exit;*/
      $ids=json_decode(file_get_contents($racine."liste-agendas-20260326.json"),true);

      foreach($ids as $id) {
          $node = Node::load($id);
          if(!empty($node)) {
              $data=$node->toArray();
              if(!empty($data)) {
                  echo sprintf("%s - %s <br>", $node->id(),$node->getType());
                  
                  foreach ($node->get('field_departement')->referencedEntities() as $term) {
                      $name = $term->getName();
                      $tid = $term->id();
                      $dep=$term->getName();
                  }
                    $urls[] = sprintf('https://%s.kidiklik.fr/%s',$dep,\Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$id));

                  if($node->getType() !== 'agenda') {
                    continue;
                  }

                  $dates=[];


                  foreach ($node->get('field_date')->referencedEntities() as $paragraph) {
                      $fin = $paragraph->get('field_date_de_fin')->value;
                      $deb = $paragraph->get('field_date_de_debut')->value;

                      //if((!str_contains($fin,"2017") && !str_contains($fin,"2018") && !str_contains($fin, "2019") && !str_contains($fin,"2020"))){
                        echo sprintf("-->%s - %s<br>",$deb,$fin);
                        $dates[] = [
                          "deb" => $deb,
                          "fin" => $fin
                        ];
                      //}
                  }
                  if(!empty($dates)) {
                      $new_ids[] = $id;
                      file_put_contents($racine."JSON2/node-".$id.".json", json_encode($data));
                      
                      $filtres=[];
                      file_put_contents($racine."JSON2/node-".$id."-date.json", json_encode($dates));
                      foreach ($node->get('field_filtres')->referencedEntities() as $filtre) {
                          $theme=$filtre->get('field_thematiques')->value??null;
                          $age=$filtre->get('field_tranches_d_ages')->value??null;
                          $vac=$filtre->get('field_vacances')->value??null;
                          $filtres[] = [
                              "theme" => $theme,
                              "age" => $age,
                              "vac" => $vac
                          ];

                      }
                      
                      file_put_contents($racine."JSON2/node-".$id."-filtres.json", json_encode($filtres));
                  
                  }
              }
          } else {
              $not_found[]=$id;
                  echo sprintf("%s contenu non trouvé <br>", $id);
          }

      
      }
      
      //file_put_contents($racine."liste-id.json", json_encode($new_ids));
      kint($urls);
      //file_put_contents($racine."liste-agenda-not-found-prod.csv", implode(chr(10),$urls));
      
      exit;



      while(($data=fgetcsv($file, 1000)) !== false) {
          $url=$data[0];
          /*$id=$data[0];
          $query = $database->select('node_field_data', 'n');
          $query->fields('n', ['nid', 'title']);
          $query->condition('n.nid', $id);
          $result = $query->execute()->fetchAssoc();
          if($result === false) {
              $url = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$id);
              kint($url);
              fputs($file_missing, $url.chr(10));

          }*/

        echo sprintf("<a href='%s' target='_blank'>%s</a><br>", $url,$url);


          /*if (preg_match('/sorties-moment\/(\d+)/', $url, $matches)) {
              $id = $matches[1];
              $node = Node::load($id);
              $data=$node->toArray();
              if(!empty($data)) {
                  file_put_contents($racine."JSON/node-".$id.".json", json_encode($data));

                  $dates=[];


                  foreach ($node->get('field_date')->referencedEntities() as $paragraph) {
                      $fin = $paragraph->get('field_date_de_fin')->value;
                      $deb = $paragraph->get('field_date_de_debut')->value;
                      $dates[] = [
                          "deb" => $deb,
                          "fin" => $fin
                      ];



                  }
                  $filtres=[];
                  file_put_contents($racine."JSON/node-".$id."-date.json", json_encode($dates));
                  foreach ($node->get('field_filtres')->referencedEntities() as $filtre) {
                      $theme=$filtre->get('field_thematiques')->value??null;
                      $age=$filtre->get('field_tranches_d_ages')->value??null;
                      $vac=$filtre->get('field_vacances')->value??null;
                      $filtres[] = [
                          "theme" => $theme,
                          "age" => $age,
                          "vac" => $vac
                      ];

                  }
                  file_put_contents($racine."JSON/node-".$id."-filtres.json", json_encode($filtres));


              }
              
          }*/

      }
      fclose($file);
      //$node = Node::load(298522);
      //$data = $node->toArray();
         
      //$node_prod = Node::create($data);
      //$node_prod->save();
        
      //Database::setActiveConnection();

      exit;

    $offset = filter_input(INPUT_GET, 'offset') ?? 0;
      $database = \Drupal::database();
      $query = $database->select('node_field_data', 'n');
        $query->fields('n', ['nid', 'title']);
        $query->condition('n.type', 'agenda');
        $query->join('node__field_date', 'nb', 'nb.entity_id = n.nid');
        $query->join('paragraph__field_date_de_fin', 'p', 'p.entity_id = nb.field_date_target_id');
        $query->addField('p', 'field_date_de_fin_value', 'date_fin');
        $query->condition('p.field_date_de_fin_value', '2022-01-01', '<');
        $query->groupBy('n.nid');
        $query->groupBy('n.title');
        $query->orderBy('p.field_date_de_fin_value');
        $query->range($offset,50);

        $result = $query->execute()->fetchAll();
        kint(count($result));
//$=fopen("/KIDIKLIK/WEBSITE/preprod.kidiklik.fr/tmp/agenda-2024-23-22.csv","a");
        foreach($result as $row) {
            $node = node::Load($row->nid);
            $value=[];
            $voir=false;
            foreach ($node->get('field_date')->referencedEntities() as $paragraph) {
                $val = $paragraph->get('field_date_de_fin')->value;
                if((str_contains($val,"2024") || str_contains($val, "2023") || str_contains($val,"2022")) && $voir===false){
                    $voir=true;
                }
                    $value[] = $paragraph->get('field_date_de_fin')->value;

            
            }
            foreach ($node->get('field_departement')->referencedEntities() as $term) {
                $name = $term->getName();
                $tid = $term->id();
                $dep=$term->getName();
            }


          $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$node->id());
          $url = sprintf("https://%s.kidiklik.fr%s",$dep,$alias);
              echo "|- ".implode(",", $value)."<br>";
          if($voir){
              echo $url."<br>";
    

                fputs($file,$url.chr(10));
            }

//kint($value);
            //$node->delete();
            //printf("%s - %s - %s<br>", $row->date_fin, $row->nid, $row->title);
        }
//fclose($);
        echo "<script>
            setTimeout(() => {
                document.location.href='?offset=".($offset+50)."'
            }, 200)
            </script>
            ";

        exit;
  }
  
  public function manageUrl() {
      $ids=[300478,
301489,
301687,
301787,
302279,
302951,
302571,
299669,
299812,
307642,
299397,
309180,
301873,
309448,
299273,
298522,
299525,
299567,
309522,
310264,
298492,
299505,
302311,
310127,
302114,
303546,
309361,
301885,
299716,
303462,
311049,
311407,
299612,
316058,
316081,
316102,
302584,
316186,
309532,
316836,
310868,
318201,
317676,
317890,
317892,
319649,
304513,
316935,
313629,
321729,
322473,
301442];

    $file=fopen("/KIDIKLIK/WEBSITE/preprod.kidiklik.fr/tmp/agenda-alias-2026.csv","w");

      foreach($ids as $id) {
          $node = Node::load($id);
          foreach ($node->get('field_departement')->referencedEntities() as $term) {
             $name = $term->getName();
             $tid = $term->id();
             $dep=$term->getName();
          }

          $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$id);
          $url = sprintf("https://%s.kidiklik.fr%s",$dep,$alias.chr(10));
        fputs($file,$url);
    }
    fclose($file);

echo "ok";
exit;
  }
}
