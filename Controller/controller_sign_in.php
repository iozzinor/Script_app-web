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
            $this->generate_view(array(
                'title'                 => _d('sign_in', 'title'),
                'navigation_menus'      => ControllerSecure::get_navigation_menus(),
                'additional_resources'  => $this->additional_resources_,
                'additional_scripts'    => $this->additional_scripts_,
                'sign_in_types_script'  => $this->make_sign_in_types_script_()
                ), 
            Router::get_base_path() . '/View/template.php');
        }

        /**
         * Throw an exception if the parameters can not be retrieved.
         * 
         * @return array The new user parameters.
         */
        private function check_sign_in_parameters_()
        {
            $parameters_to_retrieve = array('username', 'password', 'password_confirmation', 'privileges_count');
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

            // ----------
            // privileges
            // ----------

        }

        // ---------------------------------------------------------------------
        // PERFORM
        // ---------------------------------------------------------------------
        public function perform()
        {
            // check parameters
            try
            {
                $this->check_sign_in_parameters_();
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
        // UTILS
        // ---------------------------------------------------------------------
        private function make_sign_in_types_script_()
        {
            $sct = new Sct();
            $sct_topics = $sct->get_sct_topics();

            $privilege_types = $this->user_->get_all_privilege_types();

            $script_content = JavascriptGenerator::create_array('SignIn', 'sctTopics', $sct_topics, ['JavascriptGenerator', 'create_sct_topic']);
            $script_content .= JavascriptGenerator::create_array('SignIn', 'privilegeTypes', $privilege_types, ['JavascriptGenerator', 'create_privilege_type']);
            
            $script = JavascriptGenerator::generate_namespace('SignIn', $script_content);
            return JavascriptGenerator::enclose_in_script_tags($script);
        }
    }
?>