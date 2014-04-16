<?php
namespace de\take_weiland\sc_versions;

class NewVersionAction extends AbstractAction {
	
	public function __construct(Main $main) {
		parent::__construct($main);
		$main->getVersionManager()->makeWritable();
	}

	public function isSecret() {
		return true;
	}
	
	public function perform(array $request) {
		if (!isset($request['version']) || !isset($request['url'])) {
			$this->main->dieWith('Missing arguments: \'version\' and \'url\' are required!');
		}
		
		$version = $request['version'];
		$url = $request['url'];
		$versionManager = $this->main->getVersionManager();
		if ($versionManager->versionExists($version)) {
			$this->main->dieWith('Duplicate Version!');
		}
		$versionManager->setVersionInfo($version, $url);
		echo 'Version successfully created!';
	}

}