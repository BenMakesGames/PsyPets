<?php
Cache::init();

class Cache
{
    public static $PER_SCRIPT_CACHE;
    /** @var Memcache */ public static $MEMCACHE_SERVER;

    public static function init()
    {
        Cache::$PER_SCRIPT_CACHE = array();

        if(class_exists('Memcache'))
        {
            Cache::$MEMCACHE_SERVER = new Memcache;
            Cache::$MEMCACHE_SERVER->connect('localhost', 11211) or die("Someone tell That Guy Ben that the Memcached server is down.  He'll know what to do.");
        }
        else
            die("Someone tell That Guy Ben that Memcached isn't installed.  He'll know what to do.");
    }

    public static function Get($key)
    {
        if(array_key_exists($key, Cache::$PER_SCRIPT_CACHE))
            return Cache::$PER_SCRIPT_CACHE[$key];
        else
        {
            $item = Cache::$MEMCACHE_SERVER->get($key);

            Cache::AddToPerScriptCache($key, $item);

            return $item;
        }

    }

    public static function Add($key, $item)
    {
        Cache::$MEMCACHE_SERVER->set($key, $item, false, 0);
        Cache::AddToPerScriptCache($key, false);
    }

    private static function AddToPerScriptCache($key, $item)
    {
        // keep the per-script cache to no more than 50 items. keeps RAM usage reasonable.
        if(count(Cache::$PER_SCRIPT_CACHE) > 50)
            array_shift(Cache::$PER_SCRIPT_CACHE);

        Cache::$PER_SCRIPT_CACHE[$key] = $item;
    }
}
