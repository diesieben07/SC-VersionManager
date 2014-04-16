<?php
namespace de\take_weiland\sc_versions;

class RequestVersionAction extends AbstractAction {

	public function perform(array $request) {
		if (!isset($request['version'])) {
			$this->main->dieWith('Missing version parameter');
		} else {
			$version = $request['version'];
			$versionManager = $this->main->getVersionManager();
			if (!$versionManager->versionValid($version)) {
				$this->main->dieWith('Invalid version requested');
				return;
			} else {
				$versionInfo = $versionManager->getVersion($version);
				$this->printURL($versionInfo);
			}
		}
	}

	
	private function printURL($versionURL) {
		echo $versionURL;
	}
}