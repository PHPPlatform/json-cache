# PHP Platform Caching JSON Meta Data

Whole PHP Platform is built by using Meta Data in [JSON][JSON] Format.
This package provides caching for JSON Metadata

[![Build Status](https://travis-ci.org/PHPPlatform/json-cache.svg?branch=v0.1)](https://travis-ci.org/PHPPlatform/json-cache)

## Usage
 - to read from cache
``` PHP
PhpPlatform\JSONCache\Cache::getInstance()->getData($key);
```
where ``$key`` is string representaion of json path for required cached value

 - to store in cache
``` PHP
PhpPlatform\JSONCache\Cache::getInstance()->setData($data);
```
where ``$data`` is an **`array`** to be stored in the cache

 - to reset cache 
``` PHP
PhpPlatform\JSONCache\Cache::getInstance()->reset();
```


### Extending the cache
`PhpPlatform\JSONCache\Cache` can be extended to create user defined caches

``` PHP
class NewCache extends PhpPlatform\JSONCache\Cache{
	
	private static $cacheObj = null;
	protected $cacheFileName = "newcachefile"; // new cache filename
	
	public static function getInstance(){
		if(self::$cacheObj == null){
			self::$cacheObj = new NewCache();
		}
		return self::$cacheObj;
	}
}

```


## Example

Please see the test [TestCache][TestCache] for more examples 


[JSON]:http://www.json.org/
[TestCache]:https://github.com/PHPPlatform/json-cache/blob/master/tests/JSONCache/TestCache.php