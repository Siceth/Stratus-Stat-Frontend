<?php

class CacheHandler {
	
	protected static $cacheDir;
	
	public function __construct() {
		self::$cacheDir = $this->initialize();
	}
	
	public function initialize() {
		$cacheDir = $_SERVER['DOCUMENT_ROOT'].'/../cache/_stats';
		if(is_writable($cacheDir)) {
			return $cacheDir;
		} else {
			if(file_exists($cacheDir)) {
				if(chmod($cacheDir,0755)) {
					return $cacheDir;
				} else {
					echo '500 - Could not chmod cache directory!';
					die();
				}
			} else {
				mkdir($cacheDir,0775,true);
				if(is_writable($cacheDir)) {
					return $cacheDir;
				} else {
					echo '500 - Cache directory is not writable!';
					die();
				}
			}
		}
	}
	
	public function getCacheDir() {
		return self::$cacheDir;
	}
	
	public function checkCacheParams($file,$seconds) {
		$file = self::$cacheDir.'/'.$file;
		return file_exists($file)
			&& filesize($file)
			&& time()-$seconds<filemtime($file)
			&& strpos(file_get_contents($file),'<!--NOCACHE-->')===false;
	}
	
	public function writeCache($file,$input) {
		$parent = dirname(self::$cacheDir.'/'.$file);
		if (!file_exists($parent)) {
			mkdir($parent,0775,true);
		}
		return file_put_contents(self::$cacheDir.'/'.$file,$input)!==false;
	}
	
	public function purgeCache($sublocation,$match='') {
		$fs = glob(self::$cacheDir.'/'.$sublocation.'/'.$match.'*.tmp');
		foreach($fs as $f) {
			if(is_file($f)&&file_exists($f)) {
				try {
					unlink($f);
				} catch(Exception $e) {
					return false;
				}
			}
		}
		return true;
	}
}
?>