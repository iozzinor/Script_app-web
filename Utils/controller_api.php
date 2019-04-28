<?php
    require_once('controller.php');

    class ControllerApi extends Controller
    {
        protected $domain_;
        private $api_view_file_path_;

        public function __construct(Request $request, ControllerInformation $information, string $domain)
        {
            parent::__construct($request, $information);

            $this->domain_ = $domain;

            $this->api_view_file_path_ = dirname(__DIR__) . '/View/Api/' . $information->get_current_api_version() . '/view_api.php';
        }

        public function default_action()
        {
            $this->generate_view(array(
                'json_result' => array (
                    'api_version' => $this->information_->get_current_api_version(),
                    'domain' => $this->domain_
                )
            ));
        }

        public function generate_view($view_data = array(), $template = null)
        {
            $view = new View($this->api_view_file_path_);
            $view->generate($view_data, $template);
        }

        protected function get_default_json_result(bool $success = false)
        {
            return array(
                'operation_domain'  => $this->domain_,
                'action'            => $this->information_->get_action(),
                'success'           => $success
            );
        }
    }
?>