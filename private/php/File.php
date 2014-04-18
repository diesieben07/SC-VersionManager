<?php
namespace de\take_weiland\sc_versions;

class File {

	const LOCK_READ = LOCK_SH;
	const LOCK_WRITE = LOCK_EX;
	const LOCK_NONE = LOCK_UN;

	private $path;
	private $lockMode;
	private $handle;
	private $contentCache;
	private $dirty;

	public function __construct($path) {
		$this->path = $path;
		$this->lockMode = self::LOCK_NONE;
		register_shutdown_function(function() {
			try {
				$this->close();
			} catch (\Exception $e) { }
		});
	}
	
	public function exists() {
		return is_file($this->path);
	}
	
	public function lock($mode) {
		if ($this->lockMode !== $mode) {
			if ($mode !== self::LOCK_READ && $mode !== self::LOCK_WRITE && $mode !== self::LOCK_NONE) {
				throw new \InvalidArgumentException();
			}
			if ($this->lockMode === self::LOCK_READ && $mode === self::LOCK_WRITE) {
				throw new \InvalidArgumentException('Cannot write-lock after read-locking!');
			}
			if ($this->lockMode === self::LOCK_WRITE && $mode === self::LOCK_READ) {
				return;
			}
			if (flock($this->getHandle(), $mode)) {
				$this->lockMode = $mode;
			} else {
				throw new \Exception('Failed to lock file!');
			}
		}
	}
	
	public function unlock() {
		$this->lock(self::LOCK_NONE);
	}
	
	public function getContents() {
		if ($this->contentCache === null) {
			$this->lock(self::LOCK_READ);
			$contentCache = stream_get_contents($this->getHandle());
			if ($contentCache === false) {
				throw new \Exception('Failed to read file!');
			}
			$this->contentCache = $contentCache;
		}
		return $this->contentCache;
	}
	
	public function setContents($contents) {
		$this->lock(self::LOCK_WRITE);
		$this->contentCache = $contents;
		$this->dirty = true;
	}
	
	public function flushCache() {
		if ($this->dirty) {
			$handle = $this->getHandle();
			if (fseek($handle, 0) !== 0) {
				self::writeFail();
			}
			if (!ftruncate($handle, 0)) {
				self::writeFail();
			}
			if (fwrite($handle, $this->contentCache) === false) {
				self::writeFail();
			}
			if (!fflush($handle)) {
				self::writeFail();
			}
			$this->dirty = false;
		}
	}
	
	private static function writeFail() {
		throw new \Exception('Failed to write File!');
	}
	
	public function close() {
		if ($this->handle !== null) {
			$this->flushCache();
			$this->unlock();
			if (!fclose($this->handle)) {
				throw new \Exception('Failed to close File!');
			}
			$this->handle = null;
			$this->contentCache = null;
		}
	}
	
	private function getHandle() {
		if ($this->handle === null) {
			$handle = fopen($this->path, 'c+');
			
			if ($handle === false) {
				throw new \Exception('Failed to open file!');
			}
			$this->handle = $handle;
		}
		return $this->handle;
	}

}