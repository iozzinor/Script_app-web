<?php
    class ControllerAccountNotActivated extends ControllerSecure
    {
        protected function is_user_allowed($action)
        {
            return true;
        }

        public function execute_action($action)
        {
            // override the execute action method to prevent url loop
            Controller::execute_action($action);
        }

        public function default_action()
        {
            // assert that the account is not activated
            if (Login::is_account_activated())
            {
                header('Location: ' . Router::get_base_url() . 'home');
                exit;
            }

            $view_information = array(
                'title'             => _d('account_not_activated', 'Account Not Activated'),
                'navigation_menus'  => ControllerSecure::get_navigation_menus()
            );

            $this->generate_view(
                $view_information,
                Router::get_base_path() . '/View/template.php'
            );
        }
    }
?>