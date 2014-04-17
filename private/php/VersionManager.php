<?php
namespace de\take_weiland\sc_versions;

interface VersionManager {
	
	public function versionValid($version);
	
	public function versionExists($version);
	
	public function getVersion($version);
	
	public function getVersions();

}