<?php
/**
 * User: Raaghu
 */

namespace PhpPlatform\JSONCache;

/**
 * Singleton implementation for caching the metadata
 * 
 * @author raghavendra
 */
class Cache{

	/**
	 * @var Cache
	 */
	private static $cacheObj = null;
	
	private $settings = array();
	protected $cacheFileName = "e412523shyugtr345";
	
	
	protected function __construct(){
		$this->cacheFileName = sys_get_temp_dir()."/".$this->cacheFileName;
		if(is_file($this->cacheFileName)){
			$fileContents = file_get_contents($this->cacheFileName);
			$this->settings = json_decode($fileContents,true);
			if($this->settings === NULL){
				$this->settings = array();
			}
		}
	}
	
	/**
	 * @return \PhpPlatform\JSONCache\Cache
	 */
	public static function getInstance(){
		if(self::$cacheObj == null){
			self::$cacheObj = new Cache();
		}
		return self::$cacheObj;
	}
	
    /**
     * @param array $path , key path for finding the settings in cache
     * @return NULL|mixed
     */
    function getData($key) {
    	$path = self::getPaths($key);
    	if(is_array($path)){
    	    $value = $this->settings;
    	    foreach($path as $pathElem){
				if (is_array($value) && isset($value[$pathElem])) {
					$value = $value[$pathElem];
				} else {
					return NULL;
				}
			}
    		return $value;
    	}
    	return NULL;
    }

    /**
     * @param array $setting , setting to merge with current settings
     * @return boolean, TRUE on success , FALSE on failure
     */
    function setData(array $setting) {
    	
    	$originalSettings = $this->settings;
    	
    	try{
    		//read from cache file with write lock
    		$fp = fopen($this->cacheFileName, "c+");
    		$fileLock = flock($fp, LOCK_EX | LOCK_NB);
    		if($fileLock){
    			if(filesize($this->cacheFileName) > 0){
    			    $contents = fread($fp, filesize($this->cacheFileName));
    			}else{
    				$contents = "{}";
    			}
    			$this->settings = json_decode($contents,true);
    			if($this->settings === NULL){
    				$this->settings = array();
    			}
    		}else{
    			// dont do anything;
    		}
    		 
    		self::mergeData($this->settings,$setting);
    		$jsonSettings = json_encode($this->settings);
    		if($jsonSettings === FALSE){
    			throw new \Exception();
    		}
    		 
    		if($fileLock){
    			if(fwrite($fp, $jsonSettings,strlen($jsonSettings)) === FALSE){
    				throw new \Exception();
    			}
    		}else{
    			// no lock,  so dont update the cache file this time 
    		}
    		
    	}catch (\Exception $e){
    		$this->settings = $originalSettings;
    		return FALSE;
    	}finally {
    		flock($fp, LOCK_UN);
    		fclose($fp);
    	}
    	return TRUE;
    }
    
    /**
     * resets complete cache to empty
     * @return boolean, TRUE on success , FALSE on failure
     */
    public function reset(){
    	$originalSettings = $this->settings;
    	$this->settings = array();
    	if(file_put_contents($this->cacheFileName, "{}") === FALSE){
    		$this->settings = $originalSettings;
    		return FALSE;
    	}
    	return TRUE;
    }
    
    
    /**
     * 
     * this method gives array of paths for a JSON key provided
     * 
     * @param string $key
     * @return array of key paths to traverse to get the value, null if $key is not a string
     */
    private static function getPaths($key){
    	$paths = array();
    	if(!is_string($key)){
    		return null;
    	}
    	$sourcePaths = explode(".", $key);
    	foreach ($sourcePaths as $sourcePath){
    		$subPaths = preg_split('/\[(.*?)\]/',$sourcePath,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    		if($subPaths !== FALSE){
    			$paths = array_merge($paths,$subPaths);
    		}
    	}
    	return $paths;
    }
    
    /**
     * 
     * @param array|mixed $array1
     * @param array|mixed $array2
     */
    private static function mergeData(&$array1,&$array2){
    	if(is_array($array1) && is_array($array2)){
    		foreach ($array2 as $array2Key => $array2Value){
    			if(array_key_exists($array2Key, $array1)){
    				self::mergeData($array1[$array2Key], $array2[$array2Key]);
    			}else{
    				$array1[$array2Key] = $array2Value;
    			}
    		}
    	}else{
    		$array1 = $array2;
    	}
    }
    

}

?>
