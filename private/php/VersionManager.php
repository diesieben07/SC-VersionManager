<?php
namespace de\take_weiland\sc_versions;

class VersionManager {

	private $main;
	private $versionCache;
	private $versionFile;
	private $dirty;
	
	public function __construct(Main $main) {
		$this->main = $main;
		register_shutdown_function(array($this, 'close'));
	}
	
	public function makeWritable() {
		$this->openFile(true);
	}

	public function versionValid($version) {
		if (!isset($version)) {
			return false;
		}
		return preg_match('#[a-z0-9_\-\.]+#', strtolower($version));
	}
	
	public function versionExists($version) {
		return $this->versionValid($version) && isset($this->getVersions()[$version]);
	}
	
	public function getVersion($version) {
		if (!$this->versionExists($version)) {
			throw new \InvalidArgumentException('Illegal version!');
		}
		return $this->versionCache[$version];
	}
	
	public function getVersions() {
		if ($this->versionCache === null) {
			$this->openFile(false);
			if (!$this->versionFile->exists()) {
				$json = array();
			} else {
				$json = json_decode($this->versionFile->getContents(), true);
				if ($json === null) {
					$json = array();
					$this->dirty = true;
				}
			}
			$this->versionCache = $json;
		}
		return $this->versionCache;
	}
	
	public function close() {
		if ($this->versionFile !== null) {
			if ($this->dirty) {
				$this->versionFile->setContents(json_encode($this->versionCache));
			}
			$this->versionFile->close();
		}
	}
	
	public function createVersion($version, $info) {
		$this->makeWritable();
		if (!$this->versionValid($version)) {
			throw new \InvalidArgumentException('Invalid version!');
		}
		if ($this->versionExists($version)) {
			throw new \InvalidArgumentException('Duplicate Version!');
		}
		$this->versionCache[$version] = $info;
		$this->dirty = true;
	}
	
	public function deleteVersion($version) {
		$this->makeWritable();
		if (!$this->versionExists($version)) {
			throw new \InvalidArgumentException('Invalid version!');
		}
		unset($this->versionCache[$version]);
		$this->dirty = true;
	}
	
	private function openFile($writable) {
		if ($this->versionFile === null) {
			$this->versionFile = new File($this->main->getResourceDir() . '/versions.json');
		}
		if ($writable) {
			$this->versionFile->lock(File::LOCK_WRITE);
		}
	}

}