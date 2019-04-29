<?php
    require_once 'domain_route.php';

    class DomainRouteApi extends DomainRoute
    {
        public function __construct()
        {
            parent::__construct(DomainRoute::API);
        }

        public function manage_exception($exception)
        {
            $json_error = json_encode(array('error' => $exception->getMessage()));
            print($json_error);
        }

        public function resource_not_found($query)
        {
            $json_error = json_encode(array(
                'error' => 404,
                $query . ' not found'
            ));
            print($json_error);
        }
    }
?>