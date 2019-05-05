<?php
    class ControllerSignIn extends Controller
    {
        public function default_action()
        {
            $additional_resources = array();
            array_push($additional_resources, array('rel' => 'stylesheet', 'href' => '/Content/Styles/hoverable_button.css'));
            array_push($additional_resources, array('rel' => 'stylesheet', 'href' => '/Content/Styles/sign_in.css'));

            $additional_scripts = array();
            array_push($additional_scripts, array('src' => '/Content/Scripts/Dialog/dialog.js'));
            array_push($additional_scripts, array('src' => '/Content/Scripts/hoverable_button.js'));
            array_push($additional_scripts, array('src' => '/Content/Scripts/sign_in.js'));

            $this->generate_view(array(
                'title'                 => _d('sign_in', 'title'),
                'navigation_links'      => ControllerSecure::get_navigation_links(),
                'additional_resources'  => $additional_resources,
                'additional_scripts'    => $additional_scripts
                ), 
            Router::get_base_path() . '/View/template.php');
        }
    }
?>