<?php
namespace de\take_weiland\sc_versions;

class MavenVersionManager extends AbstractVersionManager {

	private $artifact;
	private $artifactBaseURL;
	private $patchNotesURL;

	public function __construct(Main $main, $repo, $group, $artifact, $patchNotesURL) {
		if (!self::validateGroup($group)) {
			dieWith('Invalid GroupID \'' . $group . '\'');
		}
		if (!self::validateArtifact($artifact)) {
			dieWith('Invalid ArtifactID!');
		}
		$url = $repo . '/';
		$url .= str_replace('.', '/', $group);
		$url .= '/' . $artifact;
		$this->artifactBaseURL = $url;
		
		$url .= '/maven-metadata.xml';
		parent::__construct($main, $url);
		$this->artifact = $artifact;
		if ($patchNotesURL !== null && !parent::urlValid($patchNotesURL)) {
			dieWith('Invalid patchNotesURL!');
		}
		$this->patchNotesURL = $patchNotesURL;
	}
	
	protected function parse($contents) {
		libxml_use_internal_errors(true); // disable E_ERROR for XML errors
		try {
			$xml = new \SimpleXMLElement($contents);
			$versions = $xml->versioning->versions;
			$parsed = array();
			foreach ($xml->versioning->versions->version as $node) {
				$info = $this->extractVersionInfo($node);
				$parsed[$info['version']] = $info;
			}
			return $parsed;
		} catch (\Exception $e) {
			dieWith('maven-metadata.xml has invalid XML!');
		}
	}
	
	private function extractVersionInfo(\SimpleXMLElement $node) {
		$version = (string) $node;
		return array(
			'version' => $version,
			'url' => $this->downloadURL($version),
			'patchNotes' => $this->makePatchNotes($version)
		);
	}
	
	private function makePatchNotes($version) {
		if ($this->patchNotesURL === null) {
			return '';
		} else {
			$url = sprintf($this->patchNotesURL, urlencode($version));
			if (!parent::urlValid($url)) {
				dieWith('Invalid patchNotesURL!');
			}
			return LazyString::create($url);
		}
	}
	
	private function downloadURL($version) {
		return $this->versionURL($version) . $this->artifact . '-' . $version . '.jar';
	}
	
	private function versionURL($version) {
		return $this->artifactBaseURL . '/' . $version . '/';
	}
	
	private static function validateArtifact($artifact) {
		return is_string($artifact) && strlen($artifact) > 0; // TODO more checks 
	}
	
	private static function validateGroup(&$group) {
		if (!is_string($group)) {
			return false;
		}
		return preg_match('#^([a-zA-Z_]{1}[a-zA-Z0-9_]*(\\.[a-zA-Z_]{1}[a-zA-Z0-9_]*)*)?$#', $group);
	}

}