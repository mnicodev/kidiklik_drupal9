<?php
$settings['domain_name'] = 'preprod.kidiklik.fr';

$databases['default']['default'] = array (
  'database' => 'drupal_8920',
  'username' => 'nmoinot',
  'password' => '8H5Pc725S',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
  'init_commands' => [
	  'sql_mode' => "SET sql_mode=''",
  ],
);
// Include Kint class.
//include_once(DRUPAL_ROOT . '/modules/contrib/devel/kint/kint/Kint.class.php');

// If debugging is very slow or leads to WSOD reduce the number
// of levels of information shown by Kint.
// Change Kint maxLevels setting:
if (class_exists('Kint')){
  // Set the maxlevels to prevent out-of-memory. Currently there doesn't seem to be a cleaner way to set this:
//  Kint::$maxLevels = 4;
}
$databases['kidiklik']['default'] = array (
  'database' => 'kidipreprod',
  'username' => 'nmoinot',
  'password' => '8H5Pc725S',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
  'init_commands' => [
    'sql_mode' => "SET sql_mode=''",
  ],
);

// Include Kint class.
//include_once(DRUPAL_ROOT . '/modules/contrib/devel/kint/kint/Kint.class.php');

// If debugging is very slow or leads to WSOD reduce the number
// of levels of information shown by Kint.
// Change Kint maxLevels setting:
if (class_exists('Kint')){
  // Set the maxlevels to prevent out-of-memory. Currently there doesn't seem to be a cleaner way to set this:
//  Kint::$maxLevels = 4;
}
