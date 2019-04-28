<?php
	session_start();
?>
<?php
	require_once 'Utils/router.php';

	$router = new Router();
	$router->route_request();
?>