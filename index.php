<?php
	session_start();
?>
<?php
	require_once 'Utils/router.php';

	$router = new Router();
	$router->route_request();

	$server = print_r($_SERVER, true);
	print('<pre>' . $server . '</pre>');

	$get = print_r($_GET, true);
	print('<pre>' . $get . '</pre>');
?>