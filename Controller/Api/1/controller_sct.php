<?php
    require_once(Configuration::get('root_path') . "Model/user.php");
    require_once(Configuration::get('root_path') . "Model/sct.php");

    class ControllerSct extends ControllerApi
    {
        /**
         * The user model.
         */
        private $user_;

        /**
         * The sct model.
         */
        private $sct_;

        public function __construct(Request $request, ControllerInformation $information)
        {
            parent::__construct($request, $information, "sct");

            $this->user_ = new User();
            $this->sct_ = new Sct();
        }

        // ---------------------------------------------------------------------
        // ADD
        // ---------------------------------------------------------------------
        public function add()
        {
        }

        // ---------------------------------------------------------------------
        // ALL
        // ---------------------------------------------------------------------
        public function all()
        {
            $this->execute_authenticated_action_('authenticated_all_');
        }

        private function authenticated_all_()
        {
            $scts_count = $this->get_scts_count_();
            $json_result = $this->get_default_json_result(true);

            if ($scts_count != null)
            {
                $json_result['information'] = $this->sct_->get_scts_information($scts_count['page_index'], $scts_count['scts_per_page']);
            }
            else
            {
                $json_result['information'] = array('scts_count' => $this->sct_->get_scts_count());
            }

            return $json_result;
        }

        // ---------------------------------------------------------------------
        // INFORMATION
        // ---------------------------------------------------------------------
        public function information()
        {
            $this->execute_authenticated_action_('authenticated_information_');
        }

        private function authenticated_information_()
        {
            if (!$this->request_->parameter_exists('id'))
            {
                throw new Exception('ID not specified.');
            }
            $sct_id = intval($this->request_->get_parameter('id'));
            if ($sct_id < 1)
            {
                throw new Exception('Wrong ID.');
            }

            $json_result = $this->get_default_json_result(true);
            $information = $this->sct_->get_sct_information($sct_id);

            if ($information == null)
            {
                throw new Exception('No SCT for this ID.');
            }
            $json_result['information'] = $information;

            return $json_result;
        }

        // ---------------------------------------------------------------------
        // NEW
        // ---------------------------------------------------------------------
        public function new()
        {
        }

        // ---------------------------------------------------------------------
        // TOP
        // ---------------------------------------------------------------------
        public function top()
        {
        }

        // ---------------------------------------------------------------------
        // FINISHED
        // ---------------------------------------------------------------------
        public function fninished()
        {
        }

        // ---------------------------------------------------------------------
        // SUBJECT
        // ---------------------------------------------------------------------
        public function subject()
        {
            $this->execute_authenticated_action_('authenticated_subject_');
        }

        private function authenticated_subject_()
        {
            return array('id' => $this->request_->get_parameter('id'));
        }

        // ---------------------------------------------------------------------
        // UTILS
        // ---------------------------------------------------------------------
        /**
         * Initialize the sct model.
         * 
         * @return int The user id, if he/she can be authenticated.
         */
        private function authenticate_user_()
        {
            if (!$this->request_->parameter_exists('credentials_key'))
            {
                throw new Exception('Not enough information to authenticate the user.');
            }
            $credentials_key = $this->request_->get_parameter('credentials_key');
            $credentials = $this->user_->check_credentials($credentials_key);

            if ($credentials == null)
            {
                throw new Exception('Can not authenticate the user.');
            }
            $this->sct_->set_user_id($credentials['user_id']);
            return $credentials['user_id'];
        }

        /**
         * Authenticate the user and execute the action.
         * 
         * @param string action The name of the action to execute.
         * This function must return an array, which is displayed as a JSON result.
         */
        private function execute_authenticated_action_($action)
        {
            $this->authenticate_user_();

            if (method_exists($this, $action))
            {
                $json_result = $this->{$action}();
            }
            else
            {
                $json_result = $this->get_default_json_result(false);
            }

            $this->generate_view(array('json_result' => $json_result));
        }

        /**
         * @return array The requested SCTs information count or null.
         */
        private function get_scts_count_()
        {
            if (!$this->request_->parameter_exists('page_index'))
            {
                return null;
            }
            $result = array(
                'page_index' => intval($this->request_->get_parameter('page_index'))
            );

            // scts per page
            if ($this->request_->parameter_exists('scts_per_page'))
            {
                $result['scts_per_page'] = intval($this->request_->get_parameter('scts_per_page'));
            }
            else
            {
                $result['scts_per_page'] = intval(Configuration::get('default_scts_per_page'));
            }

            return $result;
        }
    }
?>