<?php
namespace de\take_weiland\sc_versions;

class LazyJson {

	private $json;
	private $cache;
	
	public function __construct($json) {
		$this->json = $json;
	}
	
	public function decode() {
		if ($this->cache === null) {
			$this->cache = json_decode((string) $this->json, true);
		}
		return $this->cache;
	}
}