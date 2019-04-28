<?php
	/**
	 * This is the request class.
	 *
	 * It gathers all information about the request: POST, GET, COOKIES.
	 * It provides an accessor to parameters.
	 */
	class Request
	{
		/**
		 * The associative array of parameter sources.
		 */
		private $parameter_sources_;
			
		public function __construct()
		{
			$default_parameters = parse_ini_file(Router::get_base_path() . '/Configuration/default.ini');

			$this->parameter_sources_ = [$_COOKIE, $_GET, $_POST, $default_parameters];
		}

		/**
		 * @return Whether the parameter for the given name has been defined.
		 */
		public function parameter_exists(string $name)
		{
			if ($name == null)
			{
				return false;
			}
			for ($i = 0; $i < count($this->parameter_sources_); $i++)
			{
				if (isset($this->parameter_sources_[$i][$name]) && $this->parameter_sources_[$i][$name] != null)
				{
					return true;
				}
			}
			return false;
		}

		private function get_parameter_($name)
		{
			if ($name == null)
			{
				return false;
			}
			for ($i = 0; $i < count($this->parameter_sources_); $i++)
			{
				if (isset($this->parameter_sources_[$i][$name])
					&& $this->parameter_sources_[$i][$name] != null)
				{
					return $this->parameter_sources_[$i][$name];
				}
			}
			return null;
		}

		/**
		 * Throw an exception if the parameter is not found.
		 * 
		 * @return The parameter associated to the input name.
		 */
		public function get_parameter($name)
		{
			$result = $this->get_parameter_($name);
			if ($result != null)
			{
				return $result;
			}
			throw new Exception("Parameter with name " . $name . " does not exist.");
		}
	}
?>

