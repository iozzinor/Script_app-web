<?php
    class ControllerNotFound extends Controller
    {
        private $query_;

        public function __construct(Request $request, ControllerInformation $information, string $query)
        {
            $this->query_ = $query;

            parent::__construct($request, $information);
        }

        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            $error_message_format = _d('not_found', 'not_found_message_format');
            $error_message = sprintf($error_message_format, htmlspecialchars($this->query_));

            $this->generate_view(
                array(
                    'title'             => _d('not_found', 'title'),
                    'navigation_menus'  => ControllerSecure::get_navigation_menus(),
                    'error_message'     => $error_message
                ),
                Router::get_base_path() . '/View/template.php'
            );
        }
    }
?>