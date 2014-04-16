<?php
namespace de\take_weiland\sc_versions;

function startup($secret) {
	$private = __DIR__ . '/../private/';
	$phpDir = $private . '/php/';
	$myNamespace = __NAMESPACE__ . '\\';
	
	spl_autoload_register(function($class) use ($phpDir, $myNamespace) {
		if (startsWith($class, $myNamespace)) {
			$withoutNamespace = substr($class, strlen($myNamespace));
			require $phpDir . $withoutNamespace . '.php';
		}
	});

	$main = new Main($private . '/resources/');
	$main->parseRequest($_GET, $secret);
}

function startsWith($haystack, $needle) {
	return substr($haystack, 0, strlen($needle)) == $needle;
}