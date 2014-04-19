<?php
namespace de\take_weiland\sc_versions;

class MavenVersionManager extends AbstractVersionManager {

	private $artifact;
	private $artifactBaseURL;
	private $additionalInfoURL;

	public function __construct(Main $main, $repo, $group, $artifact, $additionalInfoURL) {
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
		if (!parent::urlValid($additionalInfoURL)) {
			dieWith('Invalid additionalInfoURL!');
		}
		$this->additionalInfoURL = $additionalInfoURL;
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
		$lazyInfo = $this->makeLazyInfo($version);
		return array(
			'version' => $version,
			'url' => $this->downloadURL($version),
			'patchNotes' => self::lazyJsonAccess($lazyInfo, $version, 'patchNotes'),
			'dependencies' => self::lazyJsonAccess($lazyInfo, $version, 'dependencies')
		);
	}
	
	private static function lazyJsonAccess(LazyJson $json, $version, $key) {
		$func = function() use ($json, $version, $key) {
			$decoded = $json->decode();
			if (!isset($decoded[$version])) {
				return '';
			}
			$versionData = $decoded[$version];
			if (isset($versionData[$key])) {
				return $versionData[$key];
			} else {
				return '';
			}
		};
		return new LazyString($func);
	}
	
	private function makeLazyInfo($version) {
		static $cache = array();
		$url = sprintf($this->additionalInfoURL, urlencode($version));
		if (!parent::urlValid($url)) {
			dieWith('Invalid additionalInfoURL!');
		}
		if (!isset($cache[$url])) {
			$json = new LazyString(function() use ($url) {
				$result = @file_get_contents($url, false, null, -1, 512);
				if ($result === false) {
					return '';
				}
				return $result;
			});
			$cache[$url] = new LazyJson($json);
		}
		return $cache[$url];
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