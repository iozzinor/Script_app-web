<?php
    class ControllerLogout extends ControllerSecure
    {
        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            unset($_SESSION['username']);
            unset($_SESSION['lang']);
            unset($_SESSION);

            session_destroy();

            header('Location: ' . Router::get_base_url() . 'home');
            exit;
        }

        protected function is_user_allowed(string $action)
        {
            return true;
        }
    }
?>