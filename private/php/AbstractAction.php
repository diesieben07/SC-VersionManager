<?php
namespace de\take_weiland\sc_versions;

abstract class AbstractAction implements Action {

	protected $main;
	
	public function __construct(Main $main) {
		$this->main = $main;
	}

	public function isSecret() {
		return false;
	}
	
	public function getMimeType() {
		return 'text/plain';
	}

}