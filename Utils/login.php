<?php
    require_once(Router::get_base_path() . '/Model/user_privilege.php');

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

        // ---------------------------------------------------------------------
        // PRIVILEGES
        // ---------------------------------------------------------------------
        /**
         * @return int The highest user privilege, or -1 in case of failure.
         */
        public static function get_highest_privilege()
        {
            $privileges = unserialize($_SESSION['user_privileges']);
            if (!isset($privileges))
            {
                return -1;
            }

            $maximum_rank = -1;

            foreach ($privileges as $privilege)
            {
                $current_privilege_rank = $privilege->get_rank();
                if ($current_privilege_rank > $maximum_rank)
                {
                    $maximum_rank = $current_privilege_rank;
                }
            }
            return $maximum_rank;
        }

        public static function get_rank_mask($privileges)
        {
            $mask = 0;

            foreach ($privileges as $privilege)
            {
                $current_privilege_rank = $privilege->get_rank();
                $mask |= $current_privilege_rank;
            }

            return $mask;
        }

        /**
         * Use the $_SESSION variable to retrieve the 'user_privilege_mask'.
         * 
         * @return bool Whether the connected user has the given privilege.
         */
        public static function has_privilege($privilege)
        {
            $mask = $_SESSION['user_privileges_mask'];
            if (!isset($mask))
            {
                return false;
            }

            return ($mask & $privilege) > 0;
        }

        public static function has_higher_privilege($privilege)
        {
            $mask = $_SESSION['user_privileges_mask'];
            if (!isset($mask))
            {
                return $mask;
            }

            return $mask >= $privilege;
        } 
    }
?>