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
                header('Location: ' . Router::get_base_url() . 'login?try_to_forward_to=' . Router::get_query());
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
            array_push($navigation_links, array('name' => _('navigation_bar_home'), 'href' => Router::get_base_url() . 'home'));

            if (Login::is_logged_in())
            {
                // new SCT subject
                array_push($navigation_links, array('name' => _('navigation_bar_new_sct_subject'), 'href' => Router::get_base_url() . 'new_sct_subject'));

                // logout
                array_push($navigation_links, array('name' => _('navigation_bar_logout'), 'href' => Router::get_base_url() . 'logout'));
            }
            else
            {
                // login
                array_push($navigation_links, array('name' => _('navigation_bar_login'), 'href' => Router::get_base_url() . 'login'));
            }


            return $navigation_links;
        }
    }
?>
