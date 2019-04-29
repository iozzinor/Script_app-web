<?php
	session_start();
?>
<?php
	require_once 'Utils/Route/router.php';

	$router = new Router();
	$router->route_request();

	$get = print_r($_GET, true);
	print('<pre>' . $get . '</pre>');
?>