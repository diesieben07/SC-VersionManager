<?php
namespace de\take_weiland\sc_versions;

class Main {

	private $resourcesDir;
	private $versionManager;
	
	public function __construct($resourcesDir) {
		$this->resourcesDir = realpath($resourcesDir);
		$this->versionManager = new VersionManager($this);
	}
	
	public function getResourceDir() {
		return $this->resourcesDir;
	}
	
	public function getVersionManager() {
		return $this->versionManager;
	}

	public function parseRequest(array $request, $secret) {
		$action = $this->findAction($request);
		if ($action->isSecret() && !$secret) {
			$this->dieWith('Requested secret action without password!');
		}
		ob_start();
		$action->perform($request);
		header('content-type: ' . $action->getMimeType());
		echo ob_get_clean();
		$this->stop();
	}
	
	public function dieWith($msg) {
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		header('content-type: text/plain');
		echo 'Error: ' . htmlspecialchars($msg);
		$this->stop();
	}
	
	private function stop() {
		$this->versionManager->close();
		exit;
	}
	
	private function findAction($request) {
		if (!isset($request['action'])) {
			return new ErrorAction($this, 'Missing Action!');
		}
		$action = $request['action'];
		switch (strtolower($action)) {
			case 'request':
				return new RequestVersionAction($this);
			case 'create':
				return new NewVersionAction($this);
			case 'list':
				return new ListVersionsAction($this);
			default:
				return new ErrorAction($this, 'Unknown Action: ' . $action);
		}
	}

}