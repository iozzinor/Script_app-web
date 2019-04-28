<?php
	// Model
	require_once 'request.php';
	require_once 'configuration.php';
	require_once 'model.php';
	require_once 'generation_time.php';

	// Controller
	require_once 'controller_information.php';
	require_once 'controller_api.php';
	require_once 'controller_secure.php';

	class Router
	{
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
				$parameters = array_merge($_GET, $_POST);
				$request = new Request($parameters);

				// check whether the query is invalid
				if ($request->parameter_exists('invalid_query'))
				{
					throw new Exception('Invalid query: ' . $request->get_parameter('invalid_query'));
				}
				else
				{
					$controller_information = $this->find_controller();
					$this->controller_information_ = $controller_information;

					$controller = $this->create_controller($controller_information, $request);

					$controller->execute_action($controller_information->get_action());
				}
			}
			catch (Exception $exception)
			{
				// display error view
				$this->manage_exception($exception);
			}
		}

		/**
		 * Find the controller to call.
		 * 
		 * @return ControllerInformation The controller information.
		 */
		protected function find_controller()
		{
			// parent path
			$parent_directory_path = dirname($_SERVER['SCRIPT_NAME']);
			$parent_directory_path = str_replace('/', '\\/', $parent_directory_path);

			$full_request = $_SERVER['REQUEST_URI'];
			$full_request = preg_replace('/' . $parent_directory_path . '/', '', $full_request);

			preg_match('/(.*)\\/([^\\/]+)\\/([^\\/?]+)\\/?/', $full_request, $matches);
			$matches_string = print_r($matches, true);

			// path components
			$components_count = count($matches);
			if ($components_count > 0)
				$requested_path = $matches[1];
			if ($components_count > 1)
				$requested_controller_name =  $matches[2];
			if ($components_count > 2)
				$requested_controller_action =  $matches[3];

			// no path
			if (!isset($requested_path))
			{
				$requested_path = preg_replace('/^\\/?/', '', $full_request);
			}
			if (!isset($requested_controller_name))
			{
				$requested_controller_name = $requested_path;
				$requested_path = '';
			}
			if (!isset($requested_controller_action))
			{
				$requested_controller_action = 'default_action';
			}

			// controller name is directory
			$test_directory_path = 'Controller/' . $requested_path . '/' . $requested_controller_name;
			if (file_exists($test_directory_path) && filetype($test_directory_path))
			{
				$requested_path .= '/' . $requested_controller_name;
				if ($requested_controller_action != 'default_action')
				{
					$requested_controller_name = $requested_controller_action;
				}
				else
				{
					$requested_controller_name = 'home';
				}

				$requested_controller_action = 'default_action';
			}

			return new ControllerInformation($requested_path, $requested_controller_name, $requested_controller_action);
		}
		
		/**
		 * Initialize a new controller.
		 * 
		 * @return Controller The new controller.
		 */
		protected function create_controller(ControllerInformation $controller_information, Request $request)
		{
			// display the not found page
			if (!file_exists($controller_information->get_file_path()))
			{
				$controller_information = new ControllerInformation('', 'not_found', 'default_action');
			}

			require($controller_information->get_file_path());
			$controller_class = $controller_information->get_class_name();

			$controller = new $controller_class($request, $controller_information);

			return $controller;
		}

		/**
		 * Handle an exception.
		 * 
		 * @param Exception The exception to handle.
		 */
		protected function manage_exception(Exception $exception)
		{
			$error_view_file_path = dirname(__DIR__) . '/View/exception.php';
			$template = dirname(__DIR__) . '/View/template.php';
			if (isset($this->controller_information_) && $this->controller_information_->belongs_to_api())
			{
				$error_view_file_path = $this->controller_information_->get_current_api_exception_view_file_path();
				$template = null;
			}

			$error_view = new View($error_view_file_path);
			$error_view->generate(array('title' => 'An error occured', 'exception' => $exception), $template);
		}
	}

?>
