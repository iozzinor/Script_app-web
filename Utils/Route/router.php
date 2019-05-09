<?php
    require_once 'domain_route_api.php';
    require_once 'domain_route_web.php';

	// Utils
	require_once(Router::get_base_path() . '/Utils/generation_time.php');
	require_once(Router::get_base_path() . '/Utils/configuration.php');
	require_once(Router::get_base_path() . '/Utils/database_handler.php');
	require_once(Router::get_base_path() . '/Utils/request.php');
	require_once(Router::get_base_path() . '/Utils/web_language.php');
	require_once(Router::get_base_path() . '/Utils/controller.php');
	require_once(Router::get_base_path() . '/Utils/controller_information.php');
	require_once(Router::get_base_path() . '/Utils/controller_secure.php');

	class Router
	{
		// ---------------------------------------------------------------------
		// CONTROLLER INFORMATION
		// ---------------------------------------------------------------------
		/**
		 * The default controller information.
		 */
		private static $default_controller_information_;

		/**
		 * The query.
		 */
		private static $query_;

		/**
		 * @return string The server base url, without the current language.
		 */
		public static function get_raw_base_url()
		{
			return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
		}

		/**
		 * @return string The server base url, adding the current language.
		 */
		public static function get_base_url()
		{
			return Router::get_raw_base_url() . WebLanguage::get_current_language()->get_short_name() . '/';
		}

		public static function get_base_path()
		{
			return $_SERVER['DOCUMENT_ROOT'];
		}

		public static function get_query()
		{
			return self::$query_;
		}

		protected static function get_default_controller_information_()
		{
			if (self::$default_controller_information_ == null)
			{
				self::$default_controller_information_ = new ControllerInformation(self::get_base_path() . '/Controller', 'home', 'default_action');
			}
			return self::$default_controller_information_;
		}

		// ---------------------------------------------------------------------
		// INSTANCE
		// ---------------------------------------------------------------------
		protected $domain_route_;

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
				$request		= new Request();
				self::$query_ 	= $request->get_parameter('q');;
				$lang 			= $request->get_parameter('lang');

				// update the language
				$this->find_language($lang);

				// get the route domain
				$this->domain_route_ = $this->find_domain_route_($request);

				// get the controller information
				$controller_information = $this->find_controller_information_(self::$query_);
				// initialize the controller
				if ($controller_information == null)
				{
					$this->domain_route_->resource_not_found(self::$query_);
				}
				else
				{
					$this->execute_controller_action(self::$query_, $request, $controller_information);
				}
			}
			catch (Exception $exception)
			{
				// display error view
				$this->manage_exception($exception);
			}
		}

		protected function find_language($lang)
		{
			WebLanguage::load_domains(array(
				'common',
				'navigation',
				'not_found',
				'home',
				'about',
				'login',
				'sign_in',
				'new_sct_subject',
				'sct_types',
				'sct_topics'
				)
			);
			$actual_lang = WebLanguage::set_language($lang);
			if ($actual_lang != $lang)
			{
				header('Location: ' . Router::get_base_url() . self::$query_);
			}
		}

		/**
		 * @param request The request.
		 * 
		 * @return RouteDomain The route domain.
		 */
		protected function find_domain_route_(Request $request)
		{
			$api_position = strpos(strtolower(self::$query_), 'api');
			if ($api_position !== false && $api_position === 0)
			{
				return new DomainRouteApi();
			}
			else if ($request->get_parameter('device') == 'mobile')
			{
				return new DomainRouteWeb(DomainRoute::MOBILE, $request);
			}

			return new DomainRouteWeb(DomainRoute::DESKTOP, $request);
		}
	
		/**
		 * @param query The query.
		 * 
		 * @return ControllerInformation The controller information.
		 */
		protected function find_controller_information_(string $query)
		{
			// break down the query into components
			$query_components = array_filter(explode('/', $query), function($component) {
				return strlen($component) > 0;
			});
			$query_components = array_map(strtolower, $query_components);

			// check components count
			if (count($query_components) == 0
				|| (count($query_components) == 1 && $query_components[0] == 'home'))
			{
				return Router::get_default_controller_information_();
			}

			// check last component as controller name
			$controller_name = array_pop($query_components);
			$path_components = array_merge([Router::get_base_path(), 'Controller'], $query_components);
			$parent_path = join('/', $path_components);
			if ($this->is_valid($parent_path, $controller_name))
			{
				return new ControllerInformation($parent_path, $controller_name, 'default_action');
			}

			// check last component as action
			$action = $controller_name;
			$controller_name = array_pop($query_components);
			$path_components = array_merge([Router::get_base_path(), 'Controller'], $query_components);
			$parent_path = join('/', $path_components);
			if ($this->is_valid($parent_path, $controller_name))
			{
				return new ControllerInformation($parent_path, $controller_name, $action);
			}

			// the controller has not been found
			return null;
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
		protected function create_controller(Request $request, ControllerInformation $controller_information)
		{
			require($controller_information->get_controller_file_path());
			$controller_class_name = $controller_information->get_controller_class_name();
			return new $controller_class_name($request, $controller_information);
		}

		protected function execute_controller_action(string $query, Request $request, ControllerInformation $controller_information)
		{
			$controller = $this->create_controller($request, $controller_information);

			if (!$controller->can_execute_action($controller_information->get_action()))
			{
				$this->domain_route_->resource_not_found($query);
			}
			else
			{
				$controller->execute_action($controller_information->get_action());
			}
		}

		/**
		 * Handle an exception.
		 * 
		 * @param Exception The exception to handle.
		 */
		protected function manage_exception(Exception $exception)
		{
			$this->domain_route_->manage_exception($exception);
		}
	}

?>
