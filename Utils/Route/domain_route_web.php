<?php
    require_once 'domain_route.php';

    class DomainRouteWeb extends DomainRoute
    {
        public function __construct($domain)
        {
            parent::__construct($domain);
        }

        public function manage_exception($exception)
        {
        }

        public function resource_not_found($query)
        {
        }
    }
?>