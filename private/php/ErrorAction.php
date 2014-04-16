<?php
namespace de\take_weiland\sc_versions;

class ErrorAction extends AbstractAction {

	private $err;
	
	public function __construct(Main $main, $err) {
		parent::__construct($main);
		$this->err = $err;
	}

	public function perform(array $request) {
		$this->main->dieWith($this->err);
	}
}