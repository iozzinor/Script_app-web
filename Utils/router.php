<?php
	// Utils
	require_once 'generation_time.php';
	require_once 'request.php';
	require_once 'controller_information.php';

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

				$controller_information = $this->find_controller_information_($query);


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
			$action = 'default_action';
			
			$i = -1;
			$current_path 	= $parent_directory_path;
			$current_name 	= '';
			$current_action = '';
			while ($i < count($query_components))
			{
				$i++;
				if ($this->is_valid($current_path, $current_name))
				{
					$parent_directory_path	= $current_path;
					$controller_name 		= $current_name;
					$action 				= strtolower($query_components[$i]);
				}

				if ($current_name != '')
				{
					$current_path = $current_path . '/' . $current_name;
				}
				$current_name = strtolower($query_components[$i]);
			}
			$action = ($action == '' ? 'default_action' : $action);

			return new ControllerInformation($parent_directory_path, $controller_name, $action);
		}

		/**
		 * @return bool Whether the combinaison of path and controller name is valid.
		 */
		protected function is_valid($controller_parent_path, $controller_name)
		{
			if (!is_dir($controller_parent_path))
			{
				return false;
			}

			return is_file($controller_parent_path . '/controller_' . $controller_name . '.php');
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
