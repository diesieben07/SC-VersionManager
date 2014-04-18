<?php
namespace de\take_weiland\sc_versions;

class Main {

	const FLOOD_CLEAN_TIME = 86400; // 24 hours
	const MAX_REQUESTS = 300; // maximum requests per IP per 24 hours. need to experiment with this probably

	private $resourcesDir;
	
	public function __construct($resourcesDir) {
		mkdir($resourcesDir, 0777, true);
		$this->resourcesDir = realpath($resourcesDir);
	}
	
	public function getResourceDir() {
		return $this->resourcesDir;
	}

	public function parseRequest(array $request, array $env, $secret) {
		$this->checkFlood($env);
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
			$request['action'] = '';
		}
		$action = $request['action'];
		switch (strtolower($action)) {
			case 'maven':
				return new MavenAction($this, $request);
			default:
				return new HelpAction($this, $request);
		}
	}
	
	private function checkFlood(array $env) {
		$file = $this->getFloodFile();
		$data = json_decode($file->getContents(), true);
		if (time() - $data['lastClean'] > self::FLOOD_CLEAN_TIME) {
			$data['data'] = array();
			$data['lastClean'] = time();
		}
		$floodData = &$data['data'];
		
		$ip = $env['REMOTE_ADDR'];
		if (!isset($floodData[$ip])) {
			$floodData[$ip] = 0;
		}
		if (++$floodData[$ip] >= self::MAX_REQUESTS) {
			dieWith('Flooding detected. No more than ' . self::MAX_REQUESTS . ' requests per IP per 24 hours.');
		}
		$file->setContents(json_encode($data));
		$file->close();
	}
	
	private function getFloodFile() {
		$file = new File($this->resourcesDir . '/floodCheck.json');
		if (!$file->exists()) {
			$file->setContents(json_encode(array('lastClean' => time(), 'data' => array())));
		} else {
			$file->lock(File::LOCK_WRITE);
		}
		return $file;
	}

}