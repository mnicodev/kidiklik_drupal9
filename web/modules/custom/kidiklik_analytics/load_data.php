<?php
	$c=curl_init();
	$opt=[
		CURLOPT_URL=>"https://www.kidiklik.fr/load_data_analytics.php",
		CURLOPT_POST=>true,
		CURLOPT_RETURNTRANSFERT=>true,
		CURLOPT_POSTFIELDS=> [
			"dep"=>$dep,
		],
	];
	curl_setopt_array($c,$opt);
	$return=curl_exec($c);
	curl_close($c);
?>
