<?php
    class ControllerInformation
    {
        /**
         * The requested path.
         */
        private $requested_path_;

        /**
         * The requested name.
         */
        private $requested_name_;

        /**
         * The controller class name.
         */
        private $class_name_;

        /**
         * The controller file path.
         */
        private $file_path_;

        /**
         * The action to execute.
         */
        private $action_;

        /**
         * @param requested_path The requested path.
         * @param requested_controller_name The path component before the last component.
         * @param requested_controller_action The last path component.
         */
        public function __construct(string $requested_path, string $requested_controller_name, string $requested_controller_action)
        {
            $this->requested_path_ = $requested_path;
            $this->requested_name_ = $requested_controller_name;

            $this->class_name_ = 'Controller' . ucfirst($requested_controller_name);
			// modify controller name to remove underscores
			while ($underscore_position = strpos($this->class_name_, '_'))
			{
				// remove the underscore
				$this->class_name_ = substr_replace($this->class_name_, '', $underscore_position, 1);

                // make the next character uppercase
                if ($underscore_position < strlen($this->class_name_))
                {
                    $this->class_name_[$underscore_position] = strtoupper($this->class_name_[$underscore_position]);
                }
            }
            
            $controller_path = 'Controller' . $requested_path;
            $this->file_path_ = $controller_path . '/controller_' . $requested_controller_name . '.php';
            $this->action_ = $requested_controller_action;
        }

        /**
         * @return string The requested path.
         */
        public function get_requested_path()
        {
            return $this->requested_path_;
        }

        /**
         * @return string The requested name.
         */
        public function get_requested_name()
        {
            return $this->requested_name_;
        }

        /**
         * @return string The controller class name.
         */
        public function get_class_name()
        {
            return $this->class_name_;
        }

        /**
         * @return string The controller file path.
         */
        public function get_file_path()
        {
            return $this->file_path_;
        }

        /**
         * @return string The requested action.
         */
        public function get_action()
        {
            return $this->action_;
        }

        /**
         * @return string The view file path.
         */
        public function get_view_file_path()
        {
            $file_path = 'View' . $this->requested_path_;
            $file_path .= '/' . $this->requested_name_;
            $file_path .= '/view_' . $this->action_ . '.php';

            return $file_path;
        }

        /**
         * @return bool Whether the current controller is a member of the API.
         */
        public function belongs_to_api()
        {
            return preg_match('/api/i', $this->requested_path_);
        }

        /**
         * @return int The current API version number.
         */
        public function get_current_api_version()
        {
            return Configuration::get('current_api_version');
        }

        /**
         * @return string The current API exception view file path.
         */
        public function get_current_api_exception_view_file_path()
        {
            $api_number = $this->get_current_api_version();
            return dirname(__DIR__) . '/View/Api/' . $api_number . '/exception.php';
        }
    }
?>