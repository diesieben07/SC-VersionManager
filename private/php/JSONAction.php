<?php
namespace de\take_weiland\sc_versions;

class JSONAction extends VersionDataAction {

	protected function createVersionManager() {
		$url = $this->requireArg('url');
		return new JSONVersionManager($this->main, $url);
	}

}