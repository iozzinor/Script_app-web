<?php
    require_once 'view.php';

    abstract class Controller
    {
        protected $request_;
        protected $information_;
        
        public function __construct(Request $request, ControllerInformation $information)
        {
            $this->request_ = $request;
            $this->information_ = $information;
        }

        /**
         * Execute an action.
         * 
         * @param action The action to execute.
         */
        public function execute_action(string $action)
        {
            if ($action == '')
            {
                $action = $this->information_->get_action();
            }
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
            $view = new View($this->information_->get_view_file_path());
            $view->generate($view_data, $template);
        }

        abstract public function default_action();
    }
?>