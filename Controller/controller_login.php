<?php
    //require_once(Router::get_base_path() . '/Model/user.php');

    class ControllerLogin extends Controller
    {
        private $additional_resources_;
        private $additional_scripts_;

        public function __construct(Request $request, ControllerInformation $information)
        {
            parent::__construct($request, $information);

            // additional resources
            $this->additional_resources_ = array();
            array_push($this->additional_resources_, array('rel' => 'stylesheet', 'href' => '/Content/Styles/login.css'));

            // additional scripts
            $this->additional_scripts_ = array();
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/Dialog/dialog.js'));
        }

        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            // check that the user is not already logged in
            if (Login::is_logged_in())
            {
                header('Location: home');
                exit;
            }

            // update the attempt number
            if (!isset($_SESSION['attempts']))
            {
                $_SESSION['attempts'] = 0;
            }
            $attempts = $_SESSION['attempts'];

            $view_information = array(
                'title'                 => _d('login', 'title'),
                'navigation_links'      => ControllerSecure::get_navigation_links(),
                'username'              => '',
                'password'              => '',
                'additional_resources'  => $this->additional_resources_,
                'additional_scripts'    => $this->additional_scripts_
            );
            // check whether the user is logging in
            if ($this->request_->parameter_exists('username') && $this->request_->parameter_exists('password'))
            {
                $attempts += 1;
                $_SESSION['attempts'] += 1;

                $username = $this->request_->get_parameter('username');
                $password = $this->request_->get_parameter('password');

                $view_information['username'] = $username;
                $view_information['password'] = $password;

                try
                {
                    if ($this->authentify_user_($username, $password))
                    {
                        $_SESSION['username'] = $username;
                        header('Location: ' . Router::get_base_url() . 'home');
                        exit;
                    }
                }
                catch (Exception $exception)
                {
                    $view_information['error'] = $exception->getMessage();
                }
            }

            $view_information['attempts'] = $attempts;

            $this->generate_view(
                $view_information,
                Router::get_base_path() . '/View/template.php'
            );
        }

        /**
         * Throw an exception if the user can not be authenticated.
         * 
         * @return bool True if the user has been authenticated.
         */
        private function authentify_user_($username, $password)
        {
            // TEMP
            return $username == "jean" && $password = "test";
            return false;
            $user = new User();

            $user_id = $user->authenticate_user($username, $password);
            if ($user_id < 0)
            {
                throw new Exception('Wrong couple username-password.');
            }
            
            return true;
        }
    }
?>