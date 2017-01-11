<?php
/**
 * User: Raaghu
 */

namespace PhpPlatform\Tests\JSONCache;

use PhpPlatform\JSONCache\Cache;

class TestCache extends \PHPUnit_Framework_TestCase{
	
	private $testCache = null;
	
	/**
	 * @before
	 */
	function setUp(){
		
		$this->testCache = array(
				"test" => array(
						"my" => array (
								"cache",
								"here"
						),
						"my-another" => array (
								"cache",
								array("here too")
						)
				)
		);
		
		$cacheInstance = Cache::getInstance();
		
		// hardcode cache using reflection
		$reflectionProperty = new \ReflectionProperty(Cache::class, "settings");
		$reflectionProperty->setAccessible(true);
		$reflectionProperty->setValue($cacheInstance, $this->testCache);
		
		$reflectionProperty = new \ReflectionProperty(Cache::class, "cacheFileName");
		$reflectionProperty->setAccessible(true);
		$cachefile = $reflectionProperty->getValue($cacheInstance);
		file_put_contents($cachefile, json_encode($this->testCache));
		
	}
	
	
	function testGetData(){
		
		// test
		parent::assertEquals(null, Cache::getInstance()->getData(null));
		parent::assertEquals(null, Cache::getInstance()->getData("non-key"));
		parent::assertEquals(null, Cache::getInstance()->getData(array("non-object")));
		parent::assertEquals($this->testCache, Cache::getInstance()->getData(""));
		parent::assertEquals($this->testCache["test"], Cache::getInstance()->getData("test"));
		parent::assertEquals($this->testCache["test"]["my"], Cache::getInstance()->getData("test.my"));
		parent::assertEquals(null, Cache::getInstance()->getData("test.my.cache"));
		parent::assertEquals($this->testCache["test"]["my-another"][0], Cache::getInstance()->getData("test.my-another[0]"));
		parent::assertEquals(null, Cache::getInstance()->getData("test.my-another[2]"));
		parent::assertEquals($this->testCache["test"]["my-another"][1][0], Cache::getInstance()->getData("test.my-another[1][0]"));
		parent::assertEquals($this->testCache["test"]["my-another"][1][0], Cache::getInstance()->getData("test.[my-another][1][0]"));
		
	}
	
	function testSetData(){
		
		Cache::getInstance()->setData(array("test"=>array("my-another"=>"cache")));
		
		parent::assertEquals("cache", Cache::getInstance()->getData("test.my-another"));
		parent::assertEquals(null, Cache::getInstance()->getData("test.my-another[0]"));
		
		Cache::getInstance()->setData(array("test"=>array("my-another-one"=>array("cache"=>"here"))));
		parent::assertEquals("here", Cache::getInstance()->getData("test.my-another-one[cache]"));
		
		parent::assertEquals($this->testCache["test"]["my"], Cache::getInstance()->getData("test.my"));
		
	}
	
	function testResetData(){
	    
		Cache::getInstance()->reset();
		
		parent::assertEquals(null, Cache::getInstance()->getData(null));
		parent::assertEquals(null, Cache::getInstance()->getData("non-key"));
		parent::assertEquals(null, Cache::getInstance()->getData(array("non-object")));
		parent::assertEquals(array(), Cache::getInstance()->getData(""));
		parent::assertEquals(null, Cache::getInstance()->getData("test"));
		
		// set after reset
		Cache::getInstance()->setData(array("test"=>array("my-another"=>"cache")));
		
		parent::assertEquals("cache", Cache::getInstance()->getData("test.my-another"));
		parent::assertEquals(null, Cache::getInstance()->getData("test.my-another[0]"));
		
		Cache::getInstance()->setData(array("test"=>array("my-another-one"=>array("cache"=>"here"))));
		parent::assertEquals("here", Cache::getInstance()->getData("test.my-another-one[cache]"));
		
		parent::assertEquals(null, Cache::getInstance()->getData("test.my"));
	
	}
	
	function testExtending(){
		
		NewCache::getInstance()->reset();
		
		parent::assertEquals(null, NewCache::getInstance()->getData(null));
		parent::assertEquals(null, NewCache::getInstance()->getData("non-key"));
		parent::assertEquals(null, NewCache::getInstance()->getData(array("non-object")));
		parent::assertEquals(array(), NewCache::getInstance()->getData(""));
		parent::assertEquals(null, NewCache::getInstance()->getData("test"));
		
		parent::assertEquals($this->testCache, Cache::getInstance()->getData(""));
		parent::assertEquals($this->testCache["test"], Cache::getInstance()->getData("test"));
		
		
		// set new Cache
		NewCache::getInstance()->setData(array("test"=>array("my-another"=>"cache")));
		
		parent::assertEquals("cache", NewCache::getInstance()->getData("test.my-another"));
		parent::assertEquals(null, NewCache::getInstance()->getData("test.my-another[0]"));
		
	}
	
}
