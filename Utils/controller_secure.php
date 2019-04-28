<?php
    require_once 'controller.php';
    require_once 'login.php';

    abstract class ControllerSecure extends Controller
    {
        // ---------------------------------------------------------------------
        // EXECUTE ACTION
        // ---------------------------------------------------------------------
        public function execute_action(string $name)
        {
            // redirect the user to the login page if he/she is not connected yet
            if (!Login::is_logged_in())
            {
                header('Location: ' . Configuration::get_base_url() . 'login');
                exit;
            }
            parent::execute_action($name);
        }

        /**
         * @return array The links that should be displayed in the navigation header.
         */
        public static function get_navigation_links()
        {
            $navigation_links = array();

            // home
            array_push($navigation_links, array('name' => 'Home', 'href' => 'home'));

            if (Login::is_logged_in())
            {
                // new SCT subject
                array_push($navigation_links, array('name' => 'New SCT Subject', 'href' => 'new_sct_subject'));

                // logout
                array_push($navigation_links, array('name' => 'Logout', 'href' => 'logout'));
            }
            else
            {
                // login
                array_push($navigation_links, array('name' => 'Login', 'href' => 'login'));
            }


            return $navigation_links;
        }
    }
?>