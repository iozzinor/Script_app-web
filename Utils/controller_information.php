<?php
    class ControllerInformation
    {
        public $parent_directory_path_;
        public $controller_name_;
        public $action_;

        public function __construct($parent_directory_path, $name, $action)
        {
            $this->parent_directory_path_ = $parent_directory_path;
            $this->controller_name_ = $name;
            $this->action_ = $action;
        }
    }
?>