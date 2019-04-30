<?php
	session_start();
?>
<?php
	require_once 'Utils/Route/router.php';

	$router = new Router();
	$router->route_request();
?>