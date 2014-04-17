<?php
namespace de\take_weiland\sc_versions;

abstract class AbstractVersionManager implements VersionManager {

	private static $validProtocols = array('http://', 'ftp://', 'https://');
	
	protected $main;
	private $url;
	private $versionCache;
	
	public function __construct(Main $main, $url) {
		$this->main = $main;
		$this->url = $url;
		if (!self::urlValid($url)) {
			dieWith('Invalid URL: \'' . $url . '\'');
		}
	}
	
	public function versionValid($version) {
		return is_string($version) && preg_match('#[a-z0-9_\-\.]+#', strtolower($version));
	}
	
	public function versionExists($version) {
		return $this->versionValid($version) && isset($this->getVersions()[$version]);
	}
	
	public function getVersion($version) {
		if (!$this->versionExists($version)) {
			throw new \InvalidArgumentException('Illegal version!');
		}
		return $this->getVersions()[$version];
	}
	
	public function getVersions() {
		if ($this->versionCache === null) {
			$contents = file_get_contents($this->url);
			if ($contents === false) {
				dieWith('URL not reachable!');
			}
			$this->versionCache = $this->parse($contents);
		}
		return $this->versionCache;
	}
	
	protected abstract function parse($contents);
	
	protected static function urlValid($url) {
		if (!is_string($url)) {
			return false;
		}
		$urlLow = strtolower($url);
		foreach (self::$validProtocols as $protocol) {
			if (startsWith($urlLow, $protocol)) {
				return true;
			}
		}
		return false;
	}

}