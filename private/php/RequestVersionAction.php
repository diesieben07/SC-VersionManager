<?php
namespace de\take_weiland\sc_versions;

class RequestVersionAction extends AbstractAction {

	public function perform() {
		$version = $this->requireArg('version');
		$versionManager = $this->main->getVersionManager();
		if (!$versionManager->versionValid($version)) {
			dieWith('Invalid version');
			return;
		} else {
			$versionInfo = $versionManager->getVersion($version);
			$this->printURL($versionInfo);
		}
	}

	
	private function printURL($versionURL) {
		echo $versionURL;
	}
}