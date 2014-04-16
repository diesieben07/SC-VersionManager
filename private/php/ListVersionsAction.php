<?php
namespace de\take_weiland\sc_versions;

class ListVersionsAction extends AbstractAction {

	public function perform() {
		$pretty = $this->getArg('pretty', 'false') === 'true';
		$versionManager = $this->main->getVersionManager();
		$versions = $versionManager->getVersions();
		$flags = JSON_FORCE_OBJECT;
		if ($pretty) {
			$flags |= JSON_PRETTY_PRINT;
		}
		echo json_encode($versions, $flags);
	}

	public function isSecret() {
		return true;
	}
	
	public function getMimeType() {
		return 'application/json';
	}

}