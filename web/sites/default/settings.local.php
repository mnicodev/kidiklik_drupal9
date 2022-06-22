<?php

/*$settings['memcache']['servers'] = ['172.18.0.1:11211' => 'default'];
$settings['memcache']['bins'] = ['default' => 'default'];
$settings['memcache']['key_prefix'] = '';*/

$settings['domain_name'] = 'kidiklik.docker';

$databases['default']['default'] = array (
  'database' => 'drupal_8920',
  'username' => 'root',
  'password' => '1234',
  'prefix' => '',
  'host' => '172.18.0.2',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
  'init_commands' => [
	  'sql_mode' => "SET sql_mode=''",
  ],
);
$databases['kidiklik']['default'] = array (
  'database' => 'kidiklik',
  'username' => 'root',
  'password' => '1234',
  'prefix' => '',
  'host' => '172.18.0.2',
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
