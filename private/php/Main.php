<?php
namespace de\take_weiland\sc_versions;

class Main {

	private $resourcesDir;
	
	public function __construct($resourcesDir) {
		$this->resourcesDir = realpath($resourcesDir);
	}
	
	public function getResourceDir() {
		return $this->resourcesDir;
	}

	public function parseRequest(array $request, $secret) {
		$action = $this->findAction($request);
		if ($action->isSecret() && !$secret) {
			dieWith('Requested secret action without password!');
		}
		ob_start();
		$action->perform();
		header('content-type: ' . $action->getMimeType());
		echo ob_get_clean();
	}
	
	private function findAction($request) {
		if (!isset($request['action'])) {
			dieWith('Missing Action!');
		}
		$action = $request['action'];
		switch (strtolower($action)) {
			case 'maven':
				return new MavenAction($this, $request);
			default:
				return new HelpAction($this, $request);
		}
	}

}