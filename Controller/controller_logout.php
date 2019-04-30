<?php
    class ControllerLogout extends ControllerSecure
    {
        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            unset($_SESSION['username']);

            session_destroy();

            header('Location: ' . Router::get_base_url() . 'home');
            exit;
        }
    }
?>