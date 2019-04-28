<?php
    class Login
    {
        /**
         * @return bool Whether the user is logged in.
         */
        public static function is_logged_in()
        {
            return isset($_SESSION['username']);
        }

        /**
         * @return string The logged user name.
         */
        public static function get_logged_username()
        {
            return $_SESSION['username'];
        }
    }
?>