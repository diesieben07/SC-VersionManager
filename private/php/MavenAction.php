<?php
namespace de\take_weiland\sc_versions;

class MavenAction extends VersionDataAction {
	
	protected function createVersionManager() {
		$repo = $this->requireArg('repo');
		$group = $this->requireArg('group');
		$artifact = $this->requireArg('artifact');
		$patchNotesURL = $this->getArg('patchNotesURL', null);
		
		return new MavenVersionManager($this->main, $repo, $group, $artifact, $patchNotesURL);
	}
	
}