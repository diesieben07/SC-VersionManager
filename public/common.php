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
	
	set_exception_handler(function(\Exception $e) {
		dieWith('Internal Exception: ' . get_class($e) . '(' . $e->getMessage() . ')');
	});

	$main = new Main($private . '/resources/');
	$main->parseRequest($_GET, $_SERVER, $secret);
}

function startsWith($haystack, $needle) {
	return substr($haystack, 0, strlen($needle)) == $needle;
}

	
function dieWith($msg) {
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	header('content-type: text/plain');
	echo 'Error: ' . htmlspecialchars($msg);
	exit;
}