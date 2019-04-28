<?php
	/**
	 * This is a the request class.
	 *
	 * It gathers all information about the request: POST and GET.
	 * It provides an accessor to parameters.
	 */
	class Request
	{
		/**
		 * The associative array of parameters.
		 */
		private $parameters_;
			
		public function __construct($parameters)
		{
			$this->parameters_ = $parameters;
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
			return isset($this->parameters_[$name]) && $this->parameters_[$name] != null;
		}

		/**
		 * Throw an exception if the parameter is not found.
		 * 
		 * @return The parameter associated to the input name.
		 */
		public function get_parameter($name)
		{
			if ($this->parameter_exists($name))
			{
				return $this->parameters_[$name];
			}
			throw new Exception("Parameter with name " . $name . " does not exist.");
		}
	}
?>

