<?php

namespace PhpPlatform\Tests\JSONCache;

use PhpPlatform\JSONCache\Cache;

class NewCache extends Cache{
	
	private static $cacheObj = null;
	protected $cacheFileName = "e412523shyugtr3451234";
	
	public static function getInstance(){
		if(self::$cacheObj == null){
			self::$cacheObj = new NewCache();
		}
		return self::$cacheObj;
	}
}