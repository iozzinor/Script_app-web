<?php
    class ControllerInformation
    {
        private $parent_directory_path_;
        private $controller_name_;
        private $action_;

        public function __construct($parent_directory_path, $name, $action)
        {
            $this->parent_directory_path_ = $parent_directory_path;
        }
    }
?>