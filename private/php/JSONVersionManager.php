<?php
namespace de\take_weiland\sc_versions;

class JSONVersionManager extends AbstractVersionManager {

	protected function parse($contents) {
		$json = json_decode($contents, true);
		if ($json === null) {
			dieWith('Invalid JSON!');
		}
		return $json;
	}

}