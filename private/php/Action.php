<?php
namespace de\take_weiland\sc_versions;

interface Action {
	
	public function perform(array $request);
	
	public function isSecret();
	
	public function getMimeType();

}