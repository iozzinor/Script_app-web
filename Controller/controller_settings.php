<?php
    require_once(Router::get_base_path() . '/Model/language.php');
    require_once(Router::get_base_path() . '/Utils/javascript_generator.php');

    class ControllerSettings extends ControllerSecure
    {
        private $additional_scripts_;

        public function __construct(Request $request, ControllerInformation $information)
        {
            parent::__construct($request, $information);

            $this->additional_scripts_ = array();
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/settings.js'));
        }

        protected function is_user_allowed($action)
        {
            return true;
        }

        public function default_action()
        {
            $this->generate_view(
                array(
                    'title'                 => _d('settings', 'Settings'),
                    'navigation_menus'      => ControllerSecure::get_navigation_menus(),
                    'settings_script'       => $this->generate_settings_script_(),
                    'additional_scripts'    => $this->additional_scripts_
                ),
                'template.php'
            );
        }

        protected function generate_settings_script_()
        {
            // retrieve information
            $language = new Language();
            $sct_languages = $language->get_all_languages();

            // languages
            $script_content = 'Settings.sctLanguages = [];';
            foreach ($sct_languages as $sct_language)
            {
                $new_language = '{id:' . $sct_language->id . ',name:"' . $sct_language->name . '",shortName:"' . $sct_language->short_name. '"}';

                $script_content .= 'Settings.sctLanguages.push(' . $new_language . ');';
            }

            $script = JavascriptGenerator::generate_namespace('Settings', $script_content);

            $script .= 'Settings.currentLanguage=\'' . WebLanguage::get_current_language()->get_short_name() . '\';';

            return JavascriptGenerator::enclose_in_script_tags($script);
        }
    }
?>