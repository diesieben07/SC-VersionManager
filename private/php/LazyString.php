<?php
namespace de\take_weiland\sc_versions;

class LazyString implements \JsonSerializable {

	const MAX_LENGTH = 512;

	private static $instanceCache = array();
	
	public static function create($url) {
		if (!isset(self::$instanceCache[$url])) {
			self::$instanceCache[$url] = new self($url);
		}
		return self::$instanceCache[$url];
	}
	
	private $url;
	private $cache;

	private function __construct($url) {
		$this->url = $url;
	}
	
	public function jsonSerialize() {
		return (string) $this;
	}
	
	public function __toString() {
		if ($this->cache === null) {
			$result = @file_get_contents($this->url, false, null, -1, self::MAX_LENGTH);
			if ($result === false) {
				$this->cache = '';
			} else {
				$this->cache = $result;
			}
		}
		return $this->cache;
	}

}