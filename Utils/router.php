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
		 * The default controller information.
		 */
		private static $default_controller_information_;

		/**
		 * The not found controller information.
		 */
		private static $not_found_controller_information_;

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

		protected static function get_default_controller_information_()
		{
			if (self::$default_controller_information_ == null)
			{
				self::$default_controller_information_ = new ControllerInformation(self::get_base_path(), 'home', 'default_action');
			}
			return self::$default_controller_information_;
		}

		protected static function get_not_found_controller_information_()
		{
			if (self::$not_found_controller_information_ == null)
			{
				self::$not_found_controller_information_ = new ControllerInformation(self::get_base_path(), 'not_found', 'default_action');
			}
			return self::$not_found_controller_information_;
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

				print('directory: ' . $controller_information->parent_directory_path_ . '<br />');
				print('name: ' . $controller_information->controller_name_ . '<br />');
				print('action: ' . $controller_information->action_ . '<br />');
				
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
			$query_components = array_filter(explode('/', $query), function($component) {
				return strlen($component) > 0;
			});

			// check components count
			if (count($query_components) == 0
				|| (count($query_components) && $query_components[0] == 'home'))
			{
				return Router::get_default_controller_information_();
			}

			// check last component as controller name
			$controller_name = array_pop($query_components);
			$parent_path = Router::get_base_path() . '/Controller/' . join('/', $query_components);
			if ($this->is_valid($parent_path, $controller_name))
			{
				return new ControllerInformation($parent_path, $controller_name, 'default_action');
			}

			// check last component as action
			$action = $controller_name;
			$controller_name = array_pop($query_components);
			$parent_path = Router::get_base_path() . '/Controller/' . join('/', $query_components);
			if ($this->is_valid($parent_path, $controller_name))
			{
				return new ControllerInformation($parent_path, $controller_name, $action);
			}

			// return not found information
			return Router::get_default_controller_information_();
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
