<?php
    require_once 'controller.php';
    require_once 'login.php';
    require_once 'Navigation/navigation_menu.php';
    require_once 'Navigation/navigation_menu_item.php';

    function compare_languages($a, $b)
    {
        return strcmp($a->get_full_name(), $b->get_full_name());
    }

    abstract class ControllerSecure extends Controller
    {
        // ---------------------------------------------------------------------
        // EXECUTE ACTION
        // ---------------------------------------------------------------------
        public function execute_action(string $name)
        {
            // redirect the user to the login page if he/she is not connected yet
            if (!Login::is_logged_in() || !$this->is_user_allowed($name))
            {
                header('Location: ' . Router::get_base_url() . 'login?try_to_forward_to=' . Router::get_query());
                exit;
            }
            else if (!Login::is_account_activated())
            {
                header('Location: ' . Router::get_base_url() . 'account_not_activated');
            }
            parent::execute_action($name);
        }

        // ---------------------------------------------------------------------
        // PRIVILEGE
        // ---------------------------------------------------------------------
        /**
         * @param string $action The action requested by the user.
         * 
         * @return bool Whether the user can access the current page.
         */
        abstract protected function is_user_allowed(string $action);

        // ---------------------------------------------------------------------
        // MENUS
        // ---------------------------------------------------------------------
        private static function get_sct_navigation_menu_()
        {
            $sct_submenus = array();

            // browsing
            $sct_browsing_items = array();
            array_push($sct_browsing_items, new NavigationMenuItem(_d('navigation', 'New'), Router::get_base_url() . 'new'));
            array_push($sct_browsing_items, new NavigationMenuItem(_d('navigation', 'Top'), Router::get_base_url() . 'top'));
            $sct_browsing_menu = new NavigationMenu(_d('navigation', 'Browsing'), $sct_browsing_items);
            
            array_push($sct_submenus, $sct_browsing_menu);

            // creation
            if (Login::has_higher_privilege(UserPrivilege::$EXPERT))
            {
                $sct_creation_items = array();
                array_push($sct_creation_items, new NavigationMenuItem(_d('navigation', 'Create New Subject'), Router::get_base_url() . 'new_sct_subject'));
                array_push($sct_creation_items, new NavigationMenuItem(_d('navigation', 'Follow My Tests'), Router::get_base_url() . 'follow_sct_subject/all'));
                array_push($sct_creation_items, new NavigationMenuItem(_d('navigation', 'Correct'), Router::get_base_url() . 'correct_sct_subject'));
                $sct_creation_menu = new NavigationMenu(_d('navigation', 'Creation'), $sct_creation_items);
    
                array_push($sct_submenus, $sct_creation_menu);
            }

            $sct_menu = new NavigationMenu(_d('navigation', 'SCT'), $sct_submenus);

            return $sct_menu;
        }

        /**
         * @return array The menus that should be displayed in the navigation header.
         */
        public static function get_navigation_menus()
        {
            $menus = array();

            // home
            array_push($menus, new NavigationMenu('', array(new NavigationMenuItem(_d('navigation', 'Home'), Router::get_base_url() . 'home'))));

            // about
            array_push($menus, new NavigationMenu('', array(new NavigationMenuItem(_d('navigation', 'About'), Router::get_base_url() . 'about'))));

            if (Login::is_logged_in())
            {
                if (Login::is_account_activated())
                {
                    // sct
                    $sct_menu = self::get_sct_navigation_menu_();
                    array_push($menus, $sct_menu);

                    // profil
                    $profil_items = array();
                    array_push($profil_items, new NavigationMenuItem(_d('navigation', 'Settings'), Router::get_base_url() . 'settings'));
                    array_push($profil_items, new NavigationMenuItem(_d('navigation', 'Privileges'), Router::get_base_url() . 'privileges'));
                    array_push($profil_items, new NavigationMenuItem(_d('navigation', 'Upgrade Privileges'), Router::get_base_url() . 'privileges/upgrade'));
                    array_push($profil_items, new NavigationMenuItem(_d('navigation', 'Logout'), Router::get_base_url() . 'logout'));
                    $profil_menu = new NavigationMenu(_d('navigation', 'Profil'), $profil_items);
                    array_push($menus, $profil_menu);
                }
                else
                {
                    // logout
                    array_push($menus, new NavigationMenu('', array(new NavigationMenuItem(_d('navigation', 'Logout'), Router::get_base_url() . 'logout'))));
                }
            }
            else
            {
                // login
                array_push($menus, new NavigationMenu('', array(new NavigationMenuItem(_d('navigation', 'Login'), Router::get_base_url() . 'login'))));
            }

            // languages
            $base_url = Router::get_raw_base_url();
            $current_language = WebLanguage::get_current_language()->get_short_name();
            $query = Router::get_query();
            $web_languages = WebLanguage::get_supported_languages();
            usort($web_languages, compare_languages);

            $languages_items = array();
            foreach ($web_languages as $supported_language)
            {
                if ($supported_language->get_short_name() == $current_language)
                {
                    array_push($languages_items, new NavigationMenuItem($supported_language->get_full_name()));
                }
                else
                {
                    $language_link = $base_url . $supported_language->get_short_name() . '/' . $query;
                    array_push($languages_items, new NavigationMenuItem($supported_language->get_full_name(), $language_link));
                }
            }
            
            $languages_menu = new NavigationMenu(_d('navigation', 'Choose Language'), $languages_items);
            array_push($menus, $languages_menu);

            return $menus;
        }
    }
?>
