<?php
    require_once(Configuration::get('root_path') . "Model/user.php");

    class ControllerUser extends ControllerApi
    {
        /**
         * The user model.
         */
        private $user_;

        public function __construct(Request $request, ControllerInformation $information)
        {
            parent::__construct($request, $information, 'user');

            $this->user_ = new User();
        }

        // ---------------------------------------------------------------------
        // CREATE
        // ---------------------------------------------------------------------
        /**
         * Create a new account.
         */
        public function create()
        {
            $create_arguments = $this->get_create_arguments_();
            $this->check_create_arguments($create_arguments);
            extract($create_arguments);

            $password_view = '';
            for ($i = 0; $i < strlen($password); ++$i)
            {
                $password_view .= "*";
            }

            $account_activation_link = $this->user_->create($username, $password, $mail_address);

            $json_result = $this->get_default_json_result(true);
            
            $json_result['information'] = array(
                'username'                  => $username,
                'password'                  => $password_view,
                'mail_address'              => $mail_address,
                'acccount_activation_link'  => $account_activation_link
            );

            $this->generate_view(array('json_result' => $json_result));
        }

        /**
         * Retrieve the arguments to create the new account.
         */
        private function get_create_arguments_()
        {
            // username must be provided
            if (!$this->request_->parameter_exists('username'))
            {
                throw new Exception('Username has not been provided.');
            }
            // password must be provided
            if (!$this->request_->parameter_exists('password'))
            {
                throw new Exception('Password has not been provided.');
            }
            // mail address must be provided
            if (!$this->request_->parameter_exists('mail_address'))
            {
                throw new Exception('Mail address has not been provided.');
            }

            $username       = $this->request_->get_parameter('username');
            $password       = $this->request_->get_parameter('password');
            $mail_address   = $this->request_->get_parameter('mail_address');

            return array(
                'username'      => $username,
                'password'      => $password,
                'mail_address'  => $mail_address
            );
        }

        /**
         * Check whether the arguments are valid.
         */
        private function check_create_arguments($create_arguments)
        {
            extract($create_arguments);

            $username_min_length = Configuration::get('username_min_length');
            $username_max_length = Configuration::get('username_max_length');
            $password_min_length = Configuration::get('password_min_length');
            $password_max_length = Configuration::get('password_max_length');

            // username length
            if (strlen($username) < $username_min_length)
            {
                throw new Exception('The username length must be greater or equal than ' . $username_min_length . '.');
            }
            else if (strlen($username) > $username_max_length)
            {
                throw new Exception('The username length must be lower or equal than ' . $username_max_length . '.');
            }

            // password length
            if (strlen($password) < $password_min_length)
            {
                throw new Exception('The password length must be greater or equal than ' . $password_min_length . '.');
            }
            else if (strlen($password) > $password_max_length)
            {
                throw new Exception('The password length must be lower or equal than ' . $password_max_length . '.');
            }
            // password must contain at least one uppercase letter
            else if (!preg_match('/[A-Z]/', $password))
            {
                throw new Exception('The password must contain at least one uppercase letter.');
            }
            // password must contain at least one lowercase letter
            else if (!preg_match('/[a-z]/', $password))
            {
                throw new Exception('The password must contain at least one lowercase letter.');
            }
            // password must contain at least one digit
            else if (!preg_match('/[0-9]/', $password))
            {
                throw new Exception('The password must contain at least one digit.');
            }

            // mail validity
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9.-_]+([+][a-zA-Z0-9.-_]+)?@[a-zA-Z][a-zA-Z0-9.-_]+[.][a-zA-Z]+$/', $mail_address))
            {
                throw new Exception('The mail address is not valid.');
            }
        }

        // ---------------------------------------------------------------------
        // ACTIVATE
        // ---------------------------------------------------------------------
        public function activate()
        {
            if (!$this->request_->parameter_exists('activation_passphrase'))
            {
                throw new Exception('The activation passphrase has not been provided !');
            }

            $activation_passphrase = $this->request_->get_parameter('activation_passphrase');

            $activated = $this->user_->activate($activation_passphrase);

            $json_result = $this->get_default_json_result(true);
            $json_result['information'] = array(
                'activation_passphrase' => $activation_passphrase,
                'activated'             => $activated
            );
            $this->generate_view(array('json_result' => $json_result));
        }

        // ---------------------------------------------------------------------
        // CHECK CREDENTIALS
        // ---------------------------------------------------------------------
        /**
         * Check whether the credentials are valid for a given key.
         * 
         * If a valid username and password are given, then
         * return all the credentials.
         */
        public function check_credentials()
        {
            if ($this->request_->parameter_exists('credentials_key'))
            {
                $credentials_key = $this->request_->get_parameter('credentials_key');
                $json_result = $this->check_credentials_key_($credentials_key);
            }
            else
            {
                $this->check_username_password_();
                $username = $this->request_->get_parameter('username');
                $password = $this->request_->get_parameter('password');

                $user_id = $this->user_->authenticate_user($username, $password);
                if ($user_id < 0)
                {
                    $json_result = $this->get_default_json_result(false);
                    throw new Exception('Can not authenticate the user');
                }
                else
                {
                    $json_result = $this->get_all_credentials_($user_id);
                }
            }
            $this->generate_view(array('json_result' => $json_result));
        }

        /**
         * @return array The JSON result array.
         */
        private function check_credentials_key_($credentials_key)
        {
            $json_result = $this->get_default_json_result(true);
            $credentials = $this->user_->check_credentials($credentials_key);

            $information = array('credentials_valid' => ($credentials != null));
            if ($credentials != null)
            {
                $information['credentials'] = $credentials;

                $user_id = $credentials['user_id'];
                $information['account_activated'] = $this->user_->is_activated($user_id);
            }
            
            $json_result['information'] = $information;
            return $json_result;
        }

        private function get_all_credentials_($user_id)
        {
            $json_result = $this->get_default_json_result(true);

            $information = array();
            $information['activated'] = $this->user_->is_activated($user_id);
            $information['credentials_list'] = $this->user_->get_all_credentials($user_id);
            $json_result['information'] = $information;

            return $json_result;
        }

        // ---------------------------------------------------------------------
        // CREATE CREDENTIALS
        // ---------------------------------------------------------------------
        public function create_credentials()
        {
            $json_result = $this->get_default_json_result(true);

            // check username - password couple
            $this->check_username_password_();
            $username = $this->request_->get_parameter('username');
            $password = $this->request_->get_parameter('password');

            $user_id = $this->user_->authenticate_user($username, $password);

            if ($user_id < 0)
            {
                $json_result['success'] = false;
                throw new Exception('Can not authenticate the user');
            }
            else
            {
                $new_credentials = $this->user_->add_credentials($user_id);
                if ($new_credentials == null)
                {
                    $json_result['success'] = false;
                }
                else
                {
                    $json_result['information'] = array('new_credentials' => $new_credentials);
                }
            }

            $this->generate_view(array('json_result' => $json_result));
        }

        /**
         * @return bool True if the username and password hash have been provided.
         */
        private function check_username_password_()
        {
            if (!$this->request_->parameter_exists('username'))
            {
                throw new Exception('The username has not been provided.');
            }
            if (!$this->request_->parameter_exists('password'))
            {
                throw new Exception('The password hash has not been provided.');
            }
        }

        // ---------------------------------------------------------------------
        // REVOKE CREDENTIALS
        // ---------------------------------------------------------------------
        public function revoke_credentials()
        {
            if ($this->request_->parameter_exists('credentials_key'))
            {
                $credentials_key = $this->request_->get_parameter('credentials_key');
                $json_result = $this->revoke_credentials_key_($credentials_key);
            }
            else
            {
                $this->check_username_password_();
                $username = $this->request_->get_parameter('username');
                $password_hash = $this->request_->get_parameter('password_hash');

                $user_id = $this->user_->authenticate_user($username, $password_hash);
                if ($user_id < 0)
                {
                    $json_result = $this->get_default_json_result(false);
                    throw new Exception('Can not authenticate the user');
                }
                else
                {
                    $json_result = $this->revoke_all_credentials_($user_id);
                }
            }
            $this->generate_view(array('json_result' => $json_result));
        }

        private function revoke_credentials_key_($credentials_key)
        {
            $json_result = $this->get_default_json_result(true);
            $credentials = $this->user_->revoke_credentials($credentials_key);

            $information = array('successfully_revoked_credentials' => ($credentials != null));
            if ($credentials != null)
            {
                $information['revoked_credentials'] = $credentials;
            }
            
            $json_result['information'] = $information;
            return $json_result;
        }

        private function revoke_all_credentials_($user_id)
        {
            $json_result = $this->get_default_json_result(true);

            $json_result['information'] = $this->user_->revoke_all_credentials($user_id);

            return $json_result;
        }

        // ---------------------------------------------------------------------
        // DELETE
        // ---------------------------------------------------------------------
        public function delete()
        {
            $this->check_username_password_();
            $username = $this->request_->get_parameter('username');
            $password = $this->request_->get_parameter('password');

            $user_id = $this->user_->authenticate_user($username, $password);
            if ($user_id < 0)
            {
                $json_result = $this->get_default_json_result(false);
                throw new Exception('Can not authenticate the user');
            }
            else
            {
                $json_result = $this->get_default_json_result(true);
                $json_result['information'] = $this->user_->delete($user_id);
            }
            $this->generate_view(array('json_result' => $json_result));
        }
    }
?>