<?php
namespace Drupal\kidiklik_front_newsletter\Controller;
require "vendor/autoload.php";
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Url;
use \Mailjet\Resources;
use \Mailjet\Client;
use Symfony\Component\HttpFoundation\RedirectResponse;
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;

/**
 * Class DefaultController.
 */
class DefaultController extends ControllerBase {

    private $apiKey = 'cbe190b2ccf97ebe0d109fb5fad89e37';
    private $secretKey= 'dee8bb0254e9ed326dcd3f36a698cc69';
    private $mj;
    private $service;

    public function __construct() {
        $this->mj = new \Mailjet\Client($this->apiKey, $this->secretKey);
        $this->service = \Drupal::service('kidiklik.service');
    }




    public function sendMail() {

    }

    public function viewConfirmationMail() {
        try {
            $token = filter_input(INPUT_GET, 'token');
            /*$sql = "update inscrits_newsletters set active = 1 where token = :token";
            $database = \Drupal::database();
            $query = $database->query($sql, [
                ':token' => $token,
            ]);*/

            if(!empty($token) && $this->service->validateToken($token)) {
                $markup = '<div class="alert alert-success">Un email vous a été envoyé afin de confirmer votre inscription.</div>';
            } else  {
                $response = new RedirectResponse('/');
                $response->send();
                exit;

            }
        } catch(Exception $exception) {
            $markup = '<div class="alert alert-danger">'.$exception->getMessage().'</div>';
        }
        return [
                '#markup' => $markup
            ];
    
    }


    /**
     * sendConfirmationEmail
     *
}* @return string
     */
    public function sendConfirmationEmail() {

        $token = filter_input(INPUT_GET, 'token');
        $markup = '<div class="alert alert-danger">Une erreur est survenue. Veuillez tenter à nouveau votre inscription.</div>';
        if(!empty($token) && $this->service->validateToken($token)) {
            $info = $this->service->decodeToken($token);
            $new_token = $this->service->generateToken($info['email'], $info['dept']);
            try {
                
            
                $url_confirmation = Url::fromRoute('kidiklik_front_newsletter.confirmation', ['token' => $new_token], ['absolute' => true])->toString();
                $url = 'https://' . \Drupal::request()->getHost() . '/send_confirmation.php?email=' . $info['email'].'&token='.base64_encode($url_confirmation);

                $response = new RedirectResponse($url);
                $response->send();
                exit;
                

            } catch(Exception $exception) {
                    $markup = '<div class="alert alert-danger">'.$exception->getMessage().'</div>';
                
            }

        }
        return [
                '#markup' => $markup
            ];
    
        
    }



  /**
   * Send.
   *
   * @return string
   *   Return Hello string.
   */
  public function send() {
    $email = filter_input(INPUT_GET, 'email');
    $token = filter_input(INPUT_GET, 'token');
//   kint($token);exit; 
    if($this->service->validateToken($token)) {
        $info = $this->service->decodeToken($token);
        $email = $info['email'] ?? null;
        $dept = $info['dept'] ?? null;
         try {
            $database = \Drupal::database();
            $sql = 'select * from inscrits_newsletters where email = :email and dept=:dept';
            $query = $database->query($sql, [
                ':email' => $email,
                ':dept' => $dept,
            ]);
            $results = $query->fetch();
            $liste_newsletters = explode('|', $results->newsletter);
            foreach($liste_newsletters as $newsletter) {
                if(in_array($newsletter, $this->service->getNewslettersList())) {
            
                    $mj=$this->mj; //new \Mailjet\Client($this->apiKey,$this->secretKey);
              
                    /* récupéparation des listes de contact */
                    $filters=array("limit"=>1000);
                    $response=$mj->get(Resources::$Contactslist,array('filters'=>$filters));
                    $response->success();
                
                    $liste=$response->getData();
                    $ID="";
                    $le_dept=$dept;
                    if($le_dept === '1' || $le_dept === 1) {
                        $le_dept = '01';
                    }
                    $newsletter_liste = "LC_" . $le_dept . "_" . $newsletter;
                    foreach($liste as $item) {
                        if($item["Name"]==$newsletter_liste) {
                            // on récupére l'id de la liste
                            $ID=$item["ID"];
                            break;
                        }
                    }
                    // si l'ID est null, alors on crée la liste de contact
                    if(empty($ID)) {
                        $filters=array("Name"=>$newsletter_liste);
                        $response=$mj->post(Resources::$Contactslist,array('body'=>$filters));
                        if($response->success()) {
                            $liste=current($response->getData());
                            $ID=$liste["ID"];
                        }
                        
                    }
                    if(!empty($ID)) {
                        $body = [
                            'Action' => 'addforce',
                            'Contacts' => [
                                'Email' => $email,
                            ]
                        ];
                        $response = $mj->post(Resources::$ContactslistManagecontact, [
                            'id' => $ID,
                            'body' => [
                                'Action' => "addforce",
                                'Email'  => $email
                            ]
                        ]);
                        if(!$response->success()) {
                            $markup = '<div class="alert alert-danger">'.$response->getBody()['ErrorMessage'].'</div>';
                        } else {
                            $sql = "update inscrits_newsletters set active = 1 where token = :token";
                            $database = \Drupal::database();
                            $query = $database->query($sql, [
                                ':token' => $token
                            ]);


                            $markup = '<div class="alert alert-success">Votre inscription a bien été prise en compte.</div>';
                            //$response = new Response();
                            //return $response->setContent('ok');
                        }
                    } else {
                        $markup = '<div class="alert alert-danger err">Liste de diffusion inconnue.</div>';
                    }
                }
            }
        } catch(Exception $e) {
            $markup = '<div class="alert alert-danger">Une erreur est survenue (code = 400).</div>';
        }
    }

    return [
        '#markup' => $markup,
        '#cache' => [
            'max-age' => 0,
        ]
    ];

  }

}
