<?php
namespace de\take_weiland\sc_versions;

class LazyString implements \JsonSerializable {

	private $callback;
	private $cache;
	
	public function __construct(callable $callback) {
		$this->callback = $callback;
	}
	
	public function __toString() {
		if ($this->cache === null) {
			$cb = $this->callback;
			$this->cache = $cb();
		}
		return $this->cache;
	}
	
	public function jsonSerialize() {
		return (string) $this;
	}

}