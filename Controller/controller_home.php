<?php
    class ControllerHome extends Controller
    {
        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            $view_information = array(
                'title'                         => _d('home', "title"),
                'navigation_menus'              => ControllerSecure::get_navigation_menus(),
                'display_new_sct_subject_link'  => Login::is_logged_in()
            );
        
            $this->generate_view($view_information, Router::get_base_path() . '/View/template.php');
        }
    }
?>