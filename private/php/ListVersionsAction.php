<?php
namespace de\take_weiland\sc_versions;

class ListVersionsAction extends AbstractAction {

	public function perform(array $request) {
		$pretty = false;
		if (isset($request['pretty']) && strtolower($request['pretty']) === 'true') {
			$pretty = true;
		}
		$versionManager = $this->main->getVersionManager();
		$versions = $versionManager->getVersions();
		echo json_encode($versions, $pretty ? JSON_PRETTY_PRINT : 0);
	}

	public function isSecret() {
		return true;
	}
	
	public function getMimeType() {
		return 'application/json';
	}

}