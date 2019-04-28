<?php
    require_once(Configuration::get('root_path') . 'Model/sct.php');

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
            array_push($this->additional_resources_, array('rel' => 'stylesheet', 'href' => 'Styles/new_sct_subject.css'));
            array_push($this->additional_resources_, array('rel' => 'stylesheet', 'href' => 'Styles/hoverable_button.css'));

            // information script
            $this->sct_information_script_ = $this->make_sct_information_script_();

            // additional scripts
            $this->additional_scripts_ = array();
            array_push($this->additional_scripts_, array('src' => 'Scripts/hoverable_button.js'));
            array_push($this->additional_scripts_, array('src' => 'Scripts/disable_button.js'));
            array_push($this->additional_scripts_, array('src' => "Scripts/sct_data_type.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/sct_data.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/sct_item.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/sct_question.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/Dialog/dialog.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/New_sct_subject/sct_item_element.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/New_sct_subject/sct_question_element.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/New_sct_subject/sct_topics_selection.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/New_sct_subject/add_questions.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/New_sct_subject/edit_questions.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/New_sct_subject/settings.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/New_sct_subject/validation.js"));
            array_push($this->additional_scripts_, array('src' => "Scripts/New_sct_subject/main.js"));
        }

        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            $this->generate_view(
                array(
                    'title'                     => "New SCT Subject",
                    'navigation_links'          => ControllerSecure::get_navigation_links(),
                    'additional_resources'      => $this->additional_resources_,
                    'sct_information_script'    => $this->sct_information_script_,
                    'additional_scripts'        => $this->additional_scripts_
                ),
                Configuration::get('root_path') . '/View/template.php'
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
            $sct = new Sct();
            $sct_types = $sct->get_sct_types();
            $sct_topics = $sct->get_sct_topics();

            // open script
            $script = '<script>';
            // open namespace
            $script .= '(function(NewSctSubject){';

            // sct types
            $script .= 'NewSctSubject.sctTypes = [];';
            foreach ($sct_types as $sct_type)
            {
                $new_type = '{id:"' . $sct_type['id'] . '",name:"' . $sct_type['name'] . '"}';

                $script .= 'NewSctSubject.sctTypes.push(' . $new_type . ');';
            }

            // sct topics
            $script .= 'NewSctSubject.sctTopics = [];';
            foreach ($sct_topics as $sct_topic)
            {
                $new_topic = '{id:"' . $sct_topic['id'] . '",name:"' . $sct_topic['name'] . '"}';

                $script .= 'NewSctSubject.sctTopics.push(' . $new_topic . ');';
            }

            $script .= '})(window.NewSctSubject=window.NewSctSubject||{});'; // close namespace
            $script .= '</script>'; // close script
            return $script;
        }
    }
?>