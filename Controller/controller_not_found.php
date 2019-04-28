<?php
    class ControllerNotFound extends Controller
    {
        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            $this->generate_view(
                array('title' => "Error 404: Not found"),
                Configuration::get('root_path') . '/View/template.php'
            );
        }
    }
?>