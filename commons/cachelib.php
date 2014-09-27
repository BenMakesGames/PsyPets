<?php
if(class_exists('Memcache'))
{
  $MEMCACHE_SERVER = new Memcache;
  $MEMCACHE_SERVER->connect('localhost', 11211) or die("Someone tell That Guy Ben that the Memcached server is down.  He'll know what to do.");
}
else
  die("Someone tell That Guy Ben that Memcached isn't installed.  He'll know what to do.");

function cache_get($key)
{
  global $MEMCACHE_SERVER, $PER_SCRIPT_CACHE;

  if(array_key_exists($key, $PER_SCRIPT_CACHE))
    return $PER_SCRIPT_CACHE[$key];
  else
    return $MEMCACHE_SERVER->get($key);
}

function cache_add($key, $item)
{
  global $MEMCACHE_SERVER, $PER_SCRIPT_CACHE;

  if($item === false)
  {
    $MEMCACHE_SERVER->set($key, false, false, 0);
    $PER_SCRIPT_CACHE[$key] = false;
  }
  else
  {
    $MEMCACHE_SERVER->set($key, $item, false, 0);
    $PER_SCRIPT_CACHE[$key] = $item;
  }
}

$PER_SCRIPT_CACHE = array();
