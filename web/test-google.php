<?php

include_once("modules/custom/kidiklik_admin/GoogleAnalyticsAPI.php");
			$dep=45;
			$ga= new GoogleAnalyticsAPI("service");
			$ga->auth->setClientId("309819877541-ru6m8fq6ojt97bn00s1p4bvtrj48g6os.apps.googleusercontent.com");
			$ga->auth->setEmail('309819877541-ru6m8fq6ojt97bn00s1p4bvtrj48g6os@developer.gserviceaccount.com');
			$ga->auth->setPrivateKey('analytics/Kidiklik-6ea7cec8a979.p12'); // Chemin d'accès au fichier .p12
			$auth = $ga->auth->getAccessToken();
    
			// Accès par token
			if ($auth['http_code'] == 200) {
				$accessToken = $auth['access_token'];
				$tokenExpires = $auth['expires_in'];
				$tokenCreated = time();
			} else $error[]="GoogleAnalytics : erreur d'authentification";
			
			$ga->setAccountId('ga:41307421');
			$ga->setAccessToken($accessToken);
			$pagevues_par_jour = array(
				'metrics' => 'ga:pageviews',
				'dimensions' => 'ga:pagePath',
				'segment' => 'sessions::condition::ga:hostname=@'.$dep.'.kidiklik.fr', 
				'sort' => '-ga:pageviews',
				'max-results' => 10,
				'start-date' => '30daysAgo'
			);
			
			$visites_par_jour = array(
				'metrics' => 'ga:users',
				'dimensions' => 'ga:date',
				'segment' => 'sessions::condition::ga:hostname=@'.$dep.'.kidiklik.fr', 
				'sort' => 'ga:date',
				'start-date' => '30daysAgo'
			);
			
			$audience_stats = array(
				'metrics' => 'ga:visitors,ga:newVisits,ga:percentNewVisits,ga:visits,ga:bounces,ga:pageviews,ga:visitBounceRate',
				'segment' => 'sessions::condition::ga:hostname=@'.$dep.'.kidiklik.fr',        
				'start-date' => '30daysAgo'
			);
			$analytics["pages"] = $ga->getPageviewsByDate($pagevues_par_jour);
			$visites = $ga->getUsersByDate($visites_par_jour);
			foreach ($visites['rows'] as $key => $visite){
				$visites['rows'][$key][0] = date('Y,n-1,d', strtotime($visite[0]));   //GAPI prend un index n-1 pour les mois (ex: janvier -> 0)
			}
			$analytics["visites"] = $visites;
			
			$chiffres = $ga->getAudienceStatistics($audience_stats);
			$chiffres['rows'][0][8] = ($chiffres['rows'][0][8]);  
			$analytics["chiffres"]=$chiffres;
			print_r($analytics);
?>
