<?php
	/**
	 * The configuration class.
	 * Attempt to read the configuration files :
	 * - first: the 'production.ini' one
	 * - second: the 'development.ini' one
	 */
	class Configuration
	{
		private static $parameters_;

		/**
		 * @return array All the parameters.
		 */
		private static function get_parameters_()
		{
			if (self::$parameters_ == null)
			{
				// get the current directory path
				$parentDirectoryPath = dirname(__FILE__);
				$configurationDirectoryPath = $parentDirectoryPath . "/Configuration";
				
				$configurationPath = $configurationDirectoryPath . "/production.ini";
				if (!file_exists($configurationPath))
				{
					$configurationPath = $configurationDirectoryPath . "/development.ini";
				}

				if (!file_exists($configurationPath))
				{
					throw new Exception("Can not initialize the server for path.");
				}
				else
				{
					self::$parameters_ = parse_ini_file($configurationPath);
				}
			}
			return self::$parameters_;
		}

		/**
		 * @return string The parameter associated to the name.
		 */
		public static function get($name, $defaultValue = null)
		{
			$value = $defaultValue;
			if (isset(self::get_parameters_()[$name]))
			{
				$value = self::get_parameters_()[$name];
			}
			return $value;
		}
	}
?>
