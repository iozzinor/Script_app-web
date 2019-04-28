<?php
    class ControllerHome extends Controller
    {
        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            $view_information = array(
                'title'                         => "Home",
                'navigation_links'              => ControllerSecure::get_navigation_links(),
                'display_new_sct_subject_link'  => Login::is_logged_in()
            );
        
            $this->generate_view($view_information, Configuration::get('root_path') . '/View/template.php');
        }
    }
?>