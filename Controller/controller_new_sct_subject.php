<?php
    require_once(Router::get_base_path() . '/Model/sct.php');
    require_once(Router::get_base_path() . '/Model/language.php');

    class ControllerNewSctSubject extends ControllerSecure
    {
        private $additional_resources_;
        private $sct_information_script_;
        private $additional_scripts_;

        public function __construct(Request $request, ControllerInformation $information)
        {
            parent::__construct($request, $information);

            // additional resources
            $this->additional_resources_ = array();
            array_push($this->additional_resources_, array('rel' => 'stylesheet', 'href' => '/Content/Styles/new_sct_subject.css'));
            array_push($this->additional_resources_, array('rel' => 'stylesheet', 'href' => '/Content/Styles/hoverable_button.css'));

            // information script
            $this->sct_information_script_ = $this->make_sct_information_script_();

            // additional scripts
            $this->additional_scripts_ = array();
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/hoverable_button.js'));
            array_push($this->additional_scripts_, array('src' => '/Content/Scripts/disable_button.js'));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/sct_data_type.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/sct_data.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/sct_item.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/sct_question.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/Dialog/dialog.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/sct_item_element.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/sct_question_element.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/sct_topics_selection.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/add_questions.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/edit_questions.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/settings.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/validation.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/main.js"));
        }

        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            $this->generate_view(
                array(
                    'title'                     => _d('new_sct_subject', 'title'),
                    'navigation_links'          => ControllerSecure::get_navigation_links(),
                    'additional_resources'      => $this->additional_resources_,
                    'sct_information_script'    => $this->sct_information_script_,
                    'additional_scripts'        => $this->additional_scripts_
                ),
                Router::get_base_path() . '/View/template.php'
            );
        }

        // ---------------------------------------------------------------------
        // SEND
        // ---------------------------------------------------------------------
        public function send()
        {
        }

        // ---------------------------------------------------------------------
        // UTILS
        // ---------------------------------------------------------------------
        /**
         * Make the SCT types and SCT topics available to the javascript source code.
         */
        private function make_sct_information_script_()
        {
            // retrieve information
            $sct = new Sct();
            $sct_types = $sct->get_sct_types();
            $sct_topics = $sct->get_sct_topics();

            $language = new Language();
            $sct_languages = $language->get_all_languages();

            // open script
            $script = '<script>';
            // open namespace
            $script .= '(function(NewSctSubject){';

            // sct types
            $script .= 'NewSctSubject.sctTypes = [];';
            foreach ($sct_types as $sct_type)
            {
                $sct_type_name = _d('sct_types', $sct_type['name']);
                $new_type = '{id:"' . $sct_type['id'] . '",name:"' . $sct_type_name . '"}';

                $script .= 'NewSctSubject.sctTypes.push(' . $new_type . ');';
            }

            // sct topics
            $script .= 'NewSctSubject.sctTopics = [];';
            foreach ($sct_topics as $sct_topic)
            {
                $sct_topic_name = _d('sct_topics', $sct_topic['name']);
                $new_topic = '{id:"' . $sct_topic['id'] . '",name:"' . $sct_topic_name . '"}';

                $script .= 'NewSctSubject.sctTopics.push(' . $new_topic . ');';
            }

            // sct languages
            $script .= 'NewSctSubject.sctLanguages = [];';
            foreach ($sct_languages as $sct_language)
            {
                $new_language = '{id:' . $sct_language->id . ',name:"' . $sct_language->name . '",shortName:"' . $sct_language->short_name. '"}';

                $script .= 'NewSctSubject.sctLanguages.push(' . $new_language . ');';
            }

            $script .= '})(window.NewSctSubject=window.NewSctSubject||{});'; // close namespace
            $script .= '</script>'; // close script
            return $script;
        }
    }
?>