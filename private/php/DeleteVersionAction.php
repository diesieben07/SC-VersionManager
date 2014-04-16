<?php
namespace de\take_weiland\sc_versions;

class DeleteVersionAction extends AbstractAction {
	
	public function perform() {
		$version = $this->requireArg('version');
		$versionManager = $this->main->getVersionManager();
		if (!$versionManager->versionExists($version)) {
			dieWith('Unknown version');
		} else {
			$versionManager->deleteVersion($version);
			echo 'Version deleted.';
		}
	}
	
	public function isSecret() {
		return true;
	}

}