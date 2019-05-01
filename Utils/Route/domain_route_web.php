<?php
    require_once 'domain_route.php';

    class DomainRouteWeb extends DomainRoute
    {
        protected $request_;

        public function __construct($domain, Request $request)
        {
            parent::__construct($domain);

            $this->request_ = $request;
        }

        public function manage_exception($exception)
        {
        }

        public function resource_not_found($query)
        {
            $not_found_controller_information = new ControllerInformation(Router::get_base_path() . '/Controller', 'not_found', 'default_action');

			require($not_found_controller_information->get_controller_file_path());
            $not_found_controller_class_name = $not_found_controller_information->get_controller_class_name();
            $not_found_controller = new $not_found_controller_class_name($this->request_, $not_found_controller_information, $query);
			$not_found_controller->execute_action($not_found_controller_information->get_action());
        }
    }
?>