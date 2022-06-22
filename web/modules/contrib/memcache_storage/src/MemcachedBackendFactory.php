<?php

namespace Drupal\memcache_storage;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Cache\CacheTagsChecksumInterface;

class MemcachedBackendFactory {

  /**
   * @var \Drupal\Core\Site\Settings
   */
  protected $settings;

  /**
   * @var \Drupal\memcache_storage\DrupalMemcachedFactory
   */
  protected $memcachedFactory;

  /**
   * @var \Drupal\Core\Cache\CacheTagsChecksumInterface
   */
  protected $checksumProvider;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Constructs the MemcachedBackendFactory object.
   *
   * @param \Drupal\Core\Site\Settings $settings
   * @param \Drupal\memcache_storage\DrupalMemcachedFactory $memcached_factory
   * @param \Drupal\Core\Cache\CacheTagsChecksumInterface $checksum_provider
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  function __construct(DrupalMemcachedFactory $memcached_factory, Settings $settings, CacheTagsChecksumInterface $checksum_provider, TimeInterface $time) {
    $this->settings = $settings;
    $this->memcachedFactory = $memcached_factory;
    $this->checksumProvider = $checksum_provider;
    $this->time = $time;
  }

  /**
   * Gets MemcacheBackend for the specified cache bin.
   *
   * @param $bin
   *   The cache bin for which the object is created.
   *
   * @return \Drupal\memcache_storage\MemcachedBackend
   *   The cache backend object for the specified cache bin.
   */
  public function get($bin) {
    // Prepopulate each cache bin name with the specific prefix to have a clear
    // and human readable cache bin names everywhere.
    $bin_name = 'cache_' . $bin;

    // Get DrupalMemcache or DrupalMemcached object for the specified bin.
    $memcached = $this->memcachedFactory->get($bin_name);

    // Initialize a new object for a class that handles Drupal-specific part
    // of memcached cache backend.
    return new MemcachedBackend($bin_name, $memcached, $this->settings, $this->checksumProvider, $this->time);
  }

}
