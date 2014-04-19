<?php
namespace de\take_weiland\sc_versions;

abstract class AbstractAction implements Action {

	protected $main;
	protected $args;
	
	public function __construct(Main $main, array $args) {
		$this->main = $main;
		$this->args = $args;
	}

	public function isSecret() {
		return false;
	}
	
	public function getMimeType() {
		return 'text/plain';
	}
	
	protected function requireArg($arg) {
		$arg = strtolower($arg);
		if (!isset($this->args[$arg])) {
			dieWith('Missing argument \'' . $arg . '\'');
		} else {
			return $this->args[$arg];
		}
	}
	
	protected function getArg($arg, $default) {
		$arg = strtolower($arg);
		return isset($this->args[$arg]) ? $this->args[$arg] : $default;
	}

}