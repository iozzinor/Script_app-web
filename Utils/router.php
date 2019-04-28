<?php
	// Utils
	require_once 'generation_time.php';
	require_once 'request.php';

	class Router
	{
		// ---------------------------------------------------------------------
		// STATIC
		// ---------------------------------------------------------------------
		/**
		 * The base URI.
		 */
		private static $base_uri_ = '/Script_odont';

		/**
		 * @return string The server base url.
		 */
		public static function get_base_url()
		{
			return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . self::$base_uri_;
		}

		public static function get_base_path()
		{
			return $_SERVER['DOCUMENT_ROOT'] . '/Script_odont';
		}

		// ---------------------------------------------------------------------
		// INSTANCE
		// ---------------------------------------------------------------------
		private $controller_information_;

		public function __construct()
		{
			GenerationTime::initialize();
		}

		/**
		 * Route the request.
		 * 
		 * Display an error page if something goes wrong.
		 */
		public function route_request()
		{
			try 
			{
				// create the request
				$request = new Request();
				
				$query = $request->get_parameter('q');

				$controller_parent_path = $this->find_controller_information_($query);
			}
			catch (Exception $exception)
			{
				// display error view
				$this->manage_exception($exception);
			}
		}
	
		/**
		 * @param query The query.
		 * 
		 * @return ControllerInformation The controller information.
		 */
		protected function find_controller_information_(string $query)
		{
			// break down the query into components
			print('<p>The query: ' . $query . '</p>');
			$query_components = explode('/', $query);

			$parent_directory_path = Router::get_base_path() . '/Controller';
			$controller_name = 'home';
			$action = '';
			
			$i = -1;
			$current_path 	= $parent_directory_path;
			$current_name 	= '';
			while ($i < count($query_components))
			{
				$i++;

				print(' ---> ' . $i . ' ' . $current_path . '<br />');
				print('      ' . $current_name . '<br />');

				if ($this->is_valid($current_path, $current_name))
				{
					$parent_directory_path	= $current_path;
					$controller_name 		= $current_name;
				}

				if ($current_name != '')
				{
					$current_path = $current_path . '/' . $current_name;
				}
				$current_name = strtolower($query_components[$i]);

				if ($i < count($components))
				{
					$action = strtolower($components[$i]);
					print('ACTION: ' . $action . '<br />');
				}
				else
				{
					$action = 'default_action';
				}
			}

			print('parent: ' . $parent_directory_path . '<br />');
			print('controller: ' . $controller_name . '<br />');
			print('action: ' . $action . '<br />');
			print('---<br />');
		}

		protected function is_valid($controller_parent_path, $controller_name)
		{
			if (!is_dir($controller_parent_path))
			{
				return false;
			}

			print($controller_parent_path . '/controller_' . $controller_name . '.php<br />');
			if (is_file($controller_parent_path . '/controller_' . $controller_name . '.php'))
			{
				print('valid!<br />');
				return true;
			}
		}

		/**
		 * Initialize a new controller.
		 * 
		 * @return Controller The new controller.
		 */
		protected function create_controller($query)
		{
		}

		/**
		 * Handle an exception.
		 * 
		 * @param Exception The exception to handle.
		 */
		protected function manage_exception(Exception $exception)
		{
			print('exception: ' . $exception);
		}
	}

?>
