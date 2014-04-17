<?php
namespace de\take_weiland\sc_versions;

abstract class VersionDataAction extends AbstractAction {

	public function perform() {
		$version = $this->getArg('version', null);
		$pretty = $this->getArg('pretty', 'false') === 'true';
		$indexed = $this->getArg('indexed', 'true') === 'true';
		
		$versionManager = $this->createVersionManager();
		$useJSON = true;
		
		if ($version === null) {
			$data = $versionManager->getVersions();
			if (!$indexed) {
				$data = array_values($data);
			}
		} else {
			if (!$versionManager->versionExists($version)) {
				dieWith('Unknown version');
			}
			$param = $this->getArg('param', null);
			$data = $versionManager->getVersion($version);
			if ($param !== null) {
				if (!isset($data[$param])) {
					dieWith('Unknown param');
				}
				$data = $data[$param];
				$useJSON = false;
			}
		}
		if ($useJSON) {
			echo json_encode($data, $pretty ? JSON_PRETTY_PRINT : 0);
		} else {
			echo $data;
		}
	}

	protected abstract function createVersionManager();


}