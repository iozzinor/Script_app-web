<?php
    require_once(Router::get_base_path() . '/Model/user.php');
    require_once(Router::get_base_path() . '/Model/sct.php');

    require_once(Router::get_base_path() . '/Utils/sign_in_exception.php');
    require_once(Router::get_base_path() . '/Utils/javascript_generator.php');

    class ControllerSignIn extends Controller
    {
        private $user_;
        private $additional_resources_;
        private $additional_scripts_;

        public function __construct(Request $request, ControllerInformation $information)
        {
            $this->user_ = new User();

            $this->additional_resources_ = array();
            array_push($this->additional_resources_, array('rel' => 'stylesheet', 'href' => '/Content/Styles/hoverable_button.css'));
            array_push($this->additional_resources_, array('rel' => 'stylesheet', 'href' => '/Content/Styles/sign_in.css'));

            $this->additional_scripts_ = array();
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/Banner/banner.js'));
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/Dialog/dialog.js'));
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/hoverable_button.js'));
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/disable_button.js'));
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/Sign_in/password_information.js'));
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/Sign_in/sign_in.js'));

            parent::__construct($request, $information);
        }

        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            $view_information = array(
                'title'                 => _d('sign_in', 'title'),
                'navigation_menus'      => ControllerSecure::get_navigation_menus(),
                'additional_resources'  => $this->additional_resources_,
                'additional_scripts'    => $this->additional_scripts_
            );

            if (!$this->request_->parameter_exists('prefilled_code'))
            {
                $view_information['no_prefilled_code'] = true;
            }
            else
            {
                // wrong account activation code
                $wrong_code_script = JavascriptGenerator::generate_namespace('SignIn', 'SignIn.wrongAccountActivationCode = true;');
                $wrong_code_script = JavascriptGenerator::enclose_in_script_tags($wrong_code_script);

                $view_information['wrong_prefilled_code_script'] = $wrong_code_script;
                $view_information['wrong_prefilled_code'] = true;
            }

            $this->generate_view($view_information, Router::get_base_path() . '/View/template.php');
        }

        /**
         * Throw an exception if the parameters can not be retrieved.
         * 
         * @return array The new user parameters.
         */
        private function check_sign_in_parameters_()
        {
            $parameters_to_retrieve = array('username', 'password', 'password_confirmation', 'mail_address');
            $parameters = array();

            foreach ($parameters_to_retrieve as $parameter_name)
            {
                if (!$this->request_->parameter_exists($parameter_name))
                {
                    throw new SignInException('parameters', $parameter_name . ' is not set.');
                }
                $parameters[$parameter_name] = htmlspecialchars($this->request_->get_parameter($parameter_name));
            }

            extract($parameters);

            // --------
            // username
            // --------
            $username_length = strlen($username);
            if ($username === '')
            {
                throw new SignInException('username', 'Username is empty.');
            }
            if ($username_length < Configuration::get('username_min_length') || $username_length > Configuration::get('username_max_length'))
            {
                throw new SignInException('username', 'Invalid username length.');
            }
            if ($this->user_->is_username_in_use($username))
            {
                $username_taken_format = _d('sign_in', 'The username \'%s\' is taken.');
                throw new SignInException('username', sprintf($username_taken_format, $username));
            }

            // ---------------------
            // password confirmation
            // ---------------------
            if ($password !== $password_confirmation)
            {
                throw new SignInException('password_confirmation', 'The password confirmation does not match the password.');
            }

            // --------
            // password
            // --------
            // length
            $password_length = strlen($password);
            if ($password === '')
            {
                throw new SignInException('password', 'The password is empty.');
            }
            if ($password_length < Configuration::get('password_min_length') || $password_length > Configuration::get('password_max_length'))
            {
                throw new SignInException('password', 'Invalid password length.');
            }

            $check_expressions = array();
            // at least one lowercase character
            array_push($check_expressions, array('expression' => '/[a-z]/', 'error_message' => 'The password must contain at least one lowercase character.'));
            // at least one uppercase character
            array_push($check_expressions, array('expression' => '/[A-Z]/', 'error_message' => 'The password must contain at least one uppercase character.'));
            // at least one digit character
            array_push($check_expressions, array('expression' => '/[0-9]/', 'error_message' => 'The password must contain at least one digit.'));

            foreach($check_expressions as $check_expression)
            {
                if (!preg_match($check_expression['expression'], $password))
                {
                    throw new SignInException('password', $check_expression['error_message']);
                }
            }

            // ------------
            // mail address
            // ------------
            if (strlen($mail_address) < 1)
            {
                throw new SignInException('mail_address', 'The mail address is empty.');
            }
            // get the local and the domain parts
            preg_match('/^([^@]+[^@\x5c])[@](.+)/', $mail_address, $mail_matches);
            if (count($mail_matches) != 3)
            {
                throw new SignInException('mail_address', 'Can not extract locale and domain parts of the mail address.');
            }
            $locale_part = $mail_matches[1];
            $domain_part = $mail_matches[2];

            $mail_errors = array();
            array_push($mail_errors, array('expression' => '/^[.]/', 'message' => 'The mail %s starts with a point.'));
            array_push($mail_errors, array('expression' => '/[.]$/', 'message' => 'The mail %s ends with a point.'));
            array_push($mail_errors, array('expression' => '/[.]{2}/', 'message' => 'The mail %s contains two points in a row.'));
            array_push($mail_errors, array('expression' => '/[^a-zA-Z0-9\/_\x5c.{}|?%~#$!=&^\-]/', 'message' => 'The mail %s contains invalid characters.'));

            // check the locale
            foreach ($mail_errors as $mail_error)
            {
                if (preg_match($mail_error['expression'], $locale_part))
                {
                    throw new SignInException('mail_address', sprintf($mail_error['message'], 'locale'));
                }
            }
            // check the domain
            foreach ($mail_errors as $mail_error)
            {
                if (preg_match($mail_error['expression'], $domain_part))
                {
                    throw new SignInException('mail_address', sprintf($mail_error['message'], 'domain'));
                }
            }

            return $parameters;
        }

        // ---------------------------------------------------------------------
        // PERFORM
        // ---------------------------------------------------------------------
        public function perform()
        {
            // check parameters
            try
            {
                $parameters = $this->check_sign_in_parameters_();
            }
            catch (SignInException $exception)
            {
                header('Content-Type: application/json');
                $error_message = json_encode($exception);
                print($error_message);
                http_response_code(400);
                exit;
            }

            $_SESSION['sign_in_success'] = true;

            // add the user
            $needs_privilege_upgrade = $this->request_->parameter_exists('needs_privilege_upgrade') ? $this->request_->get_parameter('needs_privilege_upgrade') : false;
            $this->user_->add_user($parameters['username'], $parameters['password'], $parameters['mail_address'], $needs_privilege_upgrade);
        }

        // ---------------------------------------------------------------------
        // SUCCESS
        // ---------------------------------------------------------------------
        public function success()
        {
            if (!isset($_SESSION['sign_in_success']))
            {
                header('Location: ' . Router::get_base_url() . 'home');
                exit;
            }

            unset($_SESSION['sign_in_success']);

            $this->generate_view(array(
                'title'                 => _d('sign_in', 'Account Creation'),
                'navigation_menus'      => ControllerSecure::get_navigation_menus(),
                'additional_resources'  => $this->additional_resources_
            ),
            Router::get_base_path() . '/View/template.php');
        }

        // ---------------------------------------------------------------------
        // PASSWORD POLICY
        // ---------------------------------------------------------------------
        public function password_policy()
        {
            $this->generate_view(array(
                'title'                 => _d('sign_in', 'Password Policy'),
                'navigation_menus'      => ControllerSecure::get_navigation_menus(),
                'additional_resources'  => $this->additional_resources_
                ), 
            Router::get_base_path() . '/View/template.php');
        }

        // ---------------------------------------------------------------------
        // PRIVILEGES INFORMATION
        // ---------------------------------------------------------------------
        public function privileges_information()
        {
            // privileges
            $privileges = array();
            $privilege_types = $this->user_->get_all_privilege_types();
            usort($privilege_types, function($a, $b) {
                return $a['id'] - $b['id'];
            });
            // privilege type names
            $privileges['type_names'] = array_map(function($privilege_type) {
                return _d('privilege_types', $privilege_type['name']);
            }, $privilege_types);
            $privilege_types_count = count($privilege_types);

            // rights
            $privileges['rights'] = array();
            $this->add_right_($privileges['rights'], $privilege_types_count, _d('sign_in', 'Take a test'),                 [true, true, true]);
            $this->add_right_($privileges['rights'], $privilege_types_count, _d('sign_in', 'Consult test result'),         [true, true, true]);
            $this->add_right_($privileges['rights'], $privilege_types_count, _d('sign_in', 'Consult progression'),         [true, true, true]);
            $this->add_right_($privileges['rights'], $privilege_types_count, _d('sign_in', 'Personnalized suggestions'),   [true, true, true]);
            $this->add_right_($privileges['rights'], $privilege_types_count, _d('sign_in', 'Create tests'),                [true, true]);
            $this->add_right_($privileges['rights'], $privilege_types_count, _d('sign_in', 'Follow tests'),                [true, true]);
            $this->add_right_($privileges['rights'], $privilege_types_count, _d('sign_in', 'Correct tests'),               [true, true]);
            $this->add_right_($privileges['rights'], $privilege_types_count, _d('sign_in', 'Follow class results'),        [true]);

            $this->generate_view(array(
                'title'                 => _d('sign_in', 'The Privileges'),
                'navigation_menus'      => ControllerSecure::get_navigation_menus(),
                'additional_resources'  => $this->additional_resources_,
                'privileges'            => $privileges
                ), 
            Router::get_base_path() . '/View/template.php');
        }

        private function add_right_(&$rights, $count, $name, $values)
        {
            $current_count = count($values);
            if ($current_count > $count)
            {
                array_splice($values, $count);
            }
            else if ($current_count < $count)
            {
                for ($i = 0; $i < ($count - $current_count); ++$i)
                {
                    array_push($values, false);
                }
            }

            array_push($rights, array(
                'name'      => $name,
                'values'    => $values
            ));
        }

        // ---------------------------------------------------------------------
        // ACTIVATE
        // ---------------------------------------------------------------------
        public function activate()
        {
            if (!$this->request_->parameter_exists('activation_code'))
            {
                header('Location: ' . Router::get_base_url() . 'home');
                exit;
            }

            $view_information =  array(
                'title'                     => _d('sign_in', 'Account Activation'),
                'navigation_menus'          => ControllerSecure::get_navigation_menus(),
                'additional_resources'      => $this->additional_resources_);

            $activation_code = $this->request_->get_parameter('activation_code');
            $activation_information = $this->user_->activate_account($activation_code);
            if ($activation_information == null)
            {
                $view_information['invalid_activation_code'] = true;
            }
            else
            {
                $view_information['username'] = $activation_information['username'];
            }

            $this->generate_view(
                    $view_information,
                Router::get_base_path() . '/View/template.php'
            );
        }
    }
?>