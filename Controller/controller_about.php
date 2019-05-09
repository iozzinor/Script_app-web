<?php
    class ControllerAbout extends Controller
    {
        public function default_action()
        {
            $view_information = array(
                'title'             => _d('about', 'title'),
                'navigation_menus'  => ControllerSecure::get_navigation_menus()
            );
            $this->generate_view($view_information,  Router::get_base_path() . '/View/template.php');
        }
    }
?>