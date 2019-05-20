<?php
    require_once 'user_preferences.php';
    require_once 'user_privilege.php';
    require_once 'language.php';

    class User
    {
        // ---------------------------------------------------------------------
        // AUTHENTICATION
        // ---------------------------------------------------------------------
        /**
         * @return int The user id or -1 if the user can not be authenticated.
         */
        public function authenticate_user($username, $password)
        {
            $statement = DatabaseHandler::database()->prepare('SELECT id, password_hash FROM user WHERE username LIKE :username');
            $statement->execute(array(':username' => $username));

            if ($user_information = $statement->fetch())
            {
                $password_hash = $user_information['password_hash'];

                if (password_verify($password, $password_hash))
                {
                    return $user_information['id'];
                }
            }
            return -1;
        }

        // ---------------------------------------------------------------------
        // ACCOUNT
        // ---------------------------------------------------------------------
        public function is_account_activated($user_id)
        {
            $statement = DatabaseHandler::database()->prepare('SELECT account.activated AS activated FROM account INNER JOIN user on account.id = user.account_id WHERE user.id = :user_id');
            $statement->execute(array(':user_id' => $user_id));

            if (!($result = $statement->fetch(PDO::FETCH_ASSOC)))
            {
                return false;
            }
            return $result['activated'] == 1 || $result['activated'] == 3;
        }

        // ---------------------------------------------------------------------
        // PREFERENCES
        // ---------------------------------------------------------------------
        /**
         * @return UserPreferences The user preferences for the given user or null if it is not found.
         */
        public function load_preferences($user_id)
        {
            $query =    "SELECT language.short_name AS language_short_name FROM language" .
                        "   INNER JOIN (" .
                        "       SELECT preferences.language_id AS language_id" .
                        "       FROM preferences" .
                        "       INNER JOIN user" .
                        "       ON user.preferences_id = preferences.id" .
                        "       WHERE user.id = :user_id" .
                        "   ) AS query" .
                        "   ON language.id = query.language_id";
            $statement = DatabaseHandler::database()->prepare($query);
            $statement->execute(array(':user_id' => $user_id));
            if ($result = $statement->fetch())
            {
                $language_short_name = $result['language_short_name'];
                return new UserPreferences($language_short_name);
            }
            return null;
        }

        // ---------------------------------------------------------------------
        // PRIVILEGES
        // ---------------------------------------------------------------------
        /**
         * @return array(UserPrivilege) The user privileges.
         */
        public function load_privileges($user_id)
        {
            $privileges = array();
            $topics = array();
            
            $query =   'SELECT query.name,query.topic_id FROM (';
            $query .=  '    SELECT privilege_type.name AS name, privilege.sct_topic_id AS topic_id, privilege.privilege_state_id AS privilege_state_id';
            $query .=  '    FROM privilege_type INNER JOIN privilege';
            $query .=  '        ON privilege_type.id = privilege.privilege_type_id WHERE privilege.user_id = :user_id';
            $query .=  '    ) AS query';
            $query .=  ' INNER JOIN privilege_state ON privilege_state.id = query.privilege_state_id WHERE privilege_state.name LIKE "GRANTED"';
            $statement = DatabaseHandler::database()->prepare($query);
            $statement->execute(array(':user_id' => $user_id));

            // get all topics
            while ($sql_result = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $privilege_name     = $sql_result['name'];
                $privilege_topic    = $sql_result['topic_id'];
                
                if (!isset($topics[$privilege_name]))
                {
                    $topics[$privilege_name] = array();
                }                
                if (isset($privilege_topic))
                {
                    array_push($topics[$privilege_name], $privilege_topic);
                }
            }

            // make privileges objects
            foreach ($topics as $name => $associated_topics)
            {
                $new_privilege = new UserPrivilege($name, $associated_topics);
                array_push($privileges, $new_privilege);
            }
            // student by default
            if (count($privileges) == 0)
            {
                array_push($privileges, new UserPrivilege('student'));
            }

            return $privileges;
        }

        public function get_all_privilege_types()
        {
            $result = array();
            $sql_result = DatabaseHandler::database()->query('SELECT id, name FROM privilege_type;');
            while ($current_result = $sql_result->fetch(PDO::FETCH_ASSOC))
            {
                array_push($result, $current_result);
            }
            return $result;
        }

        // ---------------------------------------------------------------------
        // CREATE
        // ---------------------------------------------------------------------
        public function validate_prefilled_inscription(string $username, string $password, string $mail_address, string $prefilled_code)
        {

        }

        public function add_user(string $username, string $password, string $mail_address, bool $needs_privilege_upgrade)
        {
            $database = DatabaseHandler::database();

            // account
            $account_id = $this->create_account_($needs_privilege_upgrade);

            // preferences
            $preferences_id = $this->create_preferences_();

            $hash = password_hash($password, PASSWORD_BCRYPT);

            print($account_id) . '<br />';
            print($preferences_id) . '<br />';
            print($hash);

            // user
            $statement = $database->prepare('INSERT INTO user (username, password_hash, preferences_id, account_id) VALUES (:username, :password_hash, :preferences_id, :account_id);');
            $statement->execute(
                array(
                    ':username'         => $username,
                    ':password_hash'    => $hash,
                    ':preferences_id'   => $preferences_id,
                    ':account_id'       => $account_id
                )
            );
            $user_id = DatabaseHandler::database()->lastInsertId();

            // privilege
            $this->create_default_privilege_($user_id);
        }

        /**
         * @return int The new preferences id.
         */
        private function create_preferences_()
        {
            $language = new Language();
            $languages = $language->get_all_languages();
            
            $current_language = WebLanguage::get_current_language()->get_short_name();

            $language_id = -1;

            foreach ($languages as $available_language)
            {
                if ($available_language->short_name == $current_language)
                {
                    $language_id = $available_language->id;
                    break;
                }
            }

            $statement = DatabaseHandler::database()->prepare('INSERT INTO preferences (language_id) VALUES (:language_id);');
            $statement->execute(array('language_id' => $language_id));

            return DatabaseHandler::database()->lastInsertId();
        }

        /**
         * @return int The new account id.
         */
        private function create_account_(bool $needs_privilege_upgrade)
        {
            // activate states:
            // 0: not activated
            // 1: activated
            // 2: not activated and needs privilege upgrade
            // 3: activated and needs privilege updgrade
            $activation_code = $this->generate_activation_code_();

            $activated = $needs_privilege_upgrade ? 2 : 0;

            $statement = DatabaseHandler::database()->prepare('INSERT INTO account (activated, activation_code) VALUES (:activated, :activation_code);');
            $statement->execute(array(
                ':activated'        => $activated,
                ':activation_code'  => $activation_code
                )
            );

            return DatabaseHandler::database()->lastInsertId();
        }

        private function generate_activation_code_()
        {
            return bin2hex(random_bytes(Configuration::get('activation_code_length') / 2));
        }

        /**
         * @param int The new user id.
         */
        private function create_default_privilege_($user_id)
        {
            $statement = DatabaseHandler::database()->prepare('INSERT INTO privilege (user_id, privilege_type_id) VALUES (:user_id, :privilege_type_id);');
            $statement->execute(
                array(
                    ':user_id'              => $user_id,
                    ':privilege_type_id'    => 3 // student by default
                )
            );
        }

        /**
         * @return bool Whether the username is already in use.
         */
        public function is_username_in_use(string $username)
        {
            $statement = DatabaseHandler::database()->prepare('SELECT count(*) AS users_count FROM user WHERE username LIKE :username');
            $statement->execute(array(':username' => $username));
            if (!($result = $statement->fetch(PDO::FETCH_ASSOC)))
            {
                return false;
            }

            return $result['users_count'] > 0;
        }

        // ---------------------------------------------------------------------
        // ACTIVATE
        // ---------------------------------------------------------------------
        /**
         * @return Array The activated account, on success.
         */
        public function activate_account($activation_code)
        {
            $account_information = $this->retrieve_account_information_($activation_code);
            if ($account_information == null)
            {
                return null;
            }

            $activated = $account_information['activated'];
            if (!$this->change_activated_($account_information['account_id'], $account_information['activated']))
            {
                return null;
            }

            return $account_information;
        }

        /**
         * @return array The account information: id, activation_code.
         */
        private function retrieve_account_information_($activation_code)
        {
            $statement = DatabaseHandler::database()->prepare('SELECT account.id AS account_id, account.activated AS activated, account.activation_code AS activation_code, user.username AS username FROM account INNER JOIN user ON user.account_id = account.id WHERE account.activation_code LIKE :activation_code;');
            $statement->execute(array(':activation_code' => $activation_code));

            if (!($result = $statement->fetch(PDO::FETCH_ASSOC)))
            {
                return null;
            }
            return $result;
        }

        /**
         * @return bool Whether the account activated status has been changed.
         */
        private function change_activated_($account_id, $activated)
        {
            $new_activated = 1;
            if ($activated == 2)
            {
                $new_activated = 3;
            }
            else if ($activated != 0)
            {
                return false;
            }

            $statement = DatabaseHandler::database()->prepare('UPDATE account set activated=:activated WHERE id=:account_id;');
            $statement->execute(array(
                'activated'     => $new_activated,
                'account_id'    => $account_id
            ));

            return true;
        }

        /**
         * Update the account activated flag and set it to activated.
         * 
         * @return bool Whether the user should be redirected to the privilege upgrade page.
         */
        public function should_display_privilege_upgrade($user_id)
        {
            $database = DatabaseHandler::database();

            // get the account id and activation code
            $statement = $database->prepare('SELECT account.id AS account_id, account.activated AS activated FROM account INNER JOIN user ON account.id = user.account_id WHERE user.id = :user_id;');
            $statement->execute(array(':user_id' => $user_id));

            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            if (!$result || $result['activated'] != 3)
            {
                return false;
            }

            $statement = $database->prepare('UPDATE account SET activated=1 WHERE id=:account_id;');
            $statement->execute(array('account_id' => $result['account_id']));
            return true;
        }

        // ---------------------------------------------------------------------
        // USERS
        // ---------------------------------------------------------------------
        /**
         * @return array The list of all users.
         */
        public function get_all_users()
        {
            $result = DatabaseHandler::database()->query('SELECT * FROM user');
            return $result->fetchAll(PDO::FETCH_CLASS);
        }
    }
?>