<?php
    class ControllerInformation
    {
        /**
         * The parent directory path.
         */
        private $parent_directory_path_;

        /**
         * The controller name.
         */
        private $controller_name_;

        /**
         * The action to execute.
         */
        private $action_;

        public function __construct($parent_directory_path, $name, $action)
        {
            $this->parent_directory_path_ = $parent_directory_path;
            $this->controller_name_ = $name;
            $this->action_ = $action;
        }

        /**
         * @return string The parent directory path.
         */
        public function get_parent_directory_path()
        {
            return $this->parent_directory_path_;
        }

        /**
         * @return string The controller class name.
         */
        public function get_controller_class_name()
        {
            $result = 'Controller';

            $add_uppercase = true;
            for ($i = 0; $i < strlen($this->controller_name_); $i++)
            {
                if ($this->controller_name_[$i] == '_')
                {
                    $add_uppercase = true;
                }
                else
                {
                    if ($add_uppercase)
                    {
                        $add_uppercase = false;
                        $result .= strtoupper($this->controller_name_[$i]);
                    }
                    else
                    {
                        $result .= strtolower($this->controller_name_[$i]);
                    }
                }
            }

            return $result;
        }

        public function get_controller_name()
        {
            return $this->controller_name_;
        }

        public function get_controller_file_name()
        {
            return 'controller_' . $this->controller_name_ . '.php';
        }

        /**
         * @return string The controller file path.
         */
        public function get_controller_file_path()
        {
            return $this->parent_directory_path_ . '/' . $this->get_controller_file_name();
        }

        /**
         * @return string The action to execute.
         */
        public function get_action()
        {
            return $this->action_;
        }
    }
?>