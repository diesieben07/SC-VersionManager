<?php
namespace de\take_weiland\sc_versions;

abstract class VersionDataAction extends AbstractAction {

	public function perform() {
		$version = $this->getArg('version', null);
		if ($version === null) {
			$versionsRaw = $this->getArg('versions', null);
			if ($versionsRaw === null) {
				$versionsURL = $this->getArg('versionsURL', null);
				if ($versionsURL === null) {
					$versions = array();
				} else {
					$json = json_decode(file_get_contents($versionsURL));
					if (is_array($json) && self::onlyStrings($json)) {
						$versions = $json;
					} else {
						dieWith('Invalid JSON in filter URL!');
					}
				}
			} else {
				$versions = array_map('trim', split(',', $versionsRaw));
			}
		} else {
			$versions = array($version);
		}
		
		$pretty = $this->getArg('pretty', 'false') === 'true';
		$indexed = $this->getArg('indexed', 'true') === 'true';
		
		$versionManager = $this->createVersionManager();
		$useJSON = true;
		
		$count = count($versions);
		if ($count === 0) {
			$data = $versionManager->getVersions();
		} elseif (count($versions) !== 1) {
			$data = $versionManager->getVersions();
			$flippedVersions = array_flip($versions);
			$data = array_filter($data, function($element) use ($flippedVersions) {
				return isset($flippedVersions[$element['version']]);
			});
			if (!$indexed) {
				$data = array_values($data);
			}
		} else {
			$version = $versions[0];
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
	
	private static function onlyStrings(array $arr) {
		foreach ($arr as $value) {
			if (!is_string($value)) {
				return false;
			}
		}
		return true;
	}

	protected abstract function createVersionManager();
	
	public function getMimeType() {
		if ($this->getArg('version', null) !== null && $this->getArg('param', null) !== null) {
			return 'text/plain';
		} else {
			return 'application/json';
		}
	}
}