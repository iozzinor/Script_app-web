<?php
    require_once 'view.php';

    abstract class Controller
    {
        protected $request_;
        protected $information_;
        
        public function __construct(Request $request, ControllerInformation $controller_information)
        {
            $this->request_     = $request;
            $this->information_ = $controller_information;
        }

        /**
         * @return string The view file path.
         */
        protected function get_view_file_path_()
        {
            $main_folder_path = $this->information_->get_parent_directory_path();
            
            $result = str_replace('Controller', 'View', $main_folder_path);

            $device = strtolower($this->request_->get_parameter('device'));
            if ($device == 'mobile')
            {
                $result .= '/Mobile';
            }
            $result .= '/Desktop';

            $result .= '/' . $this->get_view_folder_name_() . '/view_' . $this->information_->get_action() . '.php';

            return $result;
        }

        protected function get_view_folder_name_()
        {
            $result = $this->information_->get_controller_name();
            if (strlen($result) > 0)
            {
                $result[0] = strtoupper($result[0]);
            }
            return $result;
        }

        /**
         * @return bool Whether this controller can execute the action.
         */
        public function can_execute_action(string $action)
        {
            return method_exists($this, $action);
        }

        /**
         * Execute an action.
         * 
         * @param action The action to execute.
         */
        public function execute_action(string $action)
        {
            if (method_exists($this, $action))
            {
                $this->{$action}();
            }
            else
            {
                throw new Exception('Class \'' . get_class($this) . '\' does not implement action \'' . $action . '\'.');
            }
        }

        /**
         * @param view_data The data the view will use.
         */
        public function generate_view($view_data = array(), $template = null)
        {
            $file_path = $this->get_view_file_path_();
            $view = new View($file_path);
            $view->generate($view_data, $template);
        }

        abstract public function default_action();
    }
?>