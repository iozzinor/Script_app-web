<?php
	session_start();
?>
<?php
	/*require_once 'Utils/router.php';

	$router = new Router();
	$router->route_request();*/
	
	/*$server = print_r($_SERVER, true);
	print('<pre>' . $server . '</pre>');*/

	$directory = __DIR__ . '/Locale';
	$domain = 'messages';
	$locale = 'fr_FR';

	putenv('LANG=' . $locale);
	$new_locale 	= setlocale(LC_ALL, $locale);
	$bound_domain 	= bindtextdomain($domain, $directory);
	$new_domain 	= textdomain($domain);

	print(dgettext('messages', 'first_test') . '<br />');

	print($new_locale 	. '<br />');
	print($bound_domain	. '<br />');
	print($new_domain 	. '<br />');
	print(getenv('LANG') . '<br />');


?>