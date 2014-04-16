<?php
namespace de\take_weiland\sc_versions;

class NewVersionAction extends AbstractAction {

	public function isSecret() {
		return true;
	}
	
	public function perform() {
		$version = $this->requireArg('version');
		$url = $this->requireArg('url');
		$versionManager = $this->main->getVersionManager();
		if ($versionManager->versionExists($version)) {
			dieWith('Duplicate version');
		}
		$versionManager->createVersion($version, $url);
		echo 'Version successfully created!';
	}

}