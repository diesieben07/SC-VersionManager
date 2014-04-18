<?php
namespace de\take_weiland\sc_versions;

class JSONVersionManager extends AbstractVersionManager {

	protected function parse($contents) {
		$json = json_decode($contents, true);
		if (!$this->JSONValid($json)) {
			dieWith('Invalid JSON!');
		}
		return $json;
	}

	private function JSONValid($json) {
		if ($json === null || !is_array($json)) {
			return false;
		}
		foreach ($json as $version => &$versionInfo) {
			if (!$this->versionValid($version)) {
				dieWith('Invalid version in JSON!');
			}
			if (!is_array($versionInfo)) {
				dieWith('Invalid JSON structure!');
			}
			$versionInfo['version'] = $version;
			if (!isset($versionInfo['url'])) {
				dieWith('Missing key \'url\' on version ' . $version . ' in JSON!');
			}
			if (!parent::urlValid($versionInfo['url'])) {
				dieWith('Invalid URL \'' . $versionInfo['url'] . '\' for version ' . $version . ' in JSON!');
			}
			foreach ($versionInfo as $info) {
				if (!is_string($info)) {
					dieWith('Invalid JSON structure!');
				}
			}
		}
		return true;
	}
}