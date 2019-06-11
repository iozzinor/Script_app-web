<?php
    require_once(Router::get_base_path() . '/Model/sct.php');
    require_once(Router::get_base_path() . '/Model/language.php');
    require_once(Router::get_base_path() . '/Utils/javascript_generator.php');

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
            array_push($this->additional_resources_, array('rel' => 'stylesheet', 'href' => '/Content/Styles/progress_bar.css'));

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
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/Progress_bar/progress_bar.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/sct_item_element.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/sct_question_element.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/sct_topics_selection.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/add_questions.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/edit_questions.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/settings.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/validation.js"));
            array_push($this->additional_scripts_, array('src' => "/Content/Scripts/New_sct_subject/main.js"));
        }

        protected function is_user_allowed($action)
        {
            return Login::has_higher_privilege(UserPrivilege::$EXPERT);
        }

        // ---------------------------------------------------------------------
        // DEFAULT ACTION
        // ---------------------------------------------------------------------
        public function default_action()
        {
            $this->generate_view(
                array(
                    'title'                     => _d('new_sct_subject', 'title'),
                    'navigation_menus'          => ControllerSecure::get_navigation_menus(),
                    'additional_resources'      => $this->additional_resources_,
                    'sct_information_script'    => $this->sct_information_script_,
                    'additional_scripts'        => $this->additional_scripts_
                ),
                'template.php'
            );
        }

        // ---------------------------------------------------------------------
        // SAVE
        // ---------------------------------------------------------------------
        public function save()
        {
            // TEMP SCT
            $items = array();
            array_push($items, new SctItem('H1', array('type' => 'text', 'data' => 'D1')));

            $questions = array(new SctQuestion('W1', 'Diagnostic', array('Endodontics', 'Surgery'), $items));
            $subject = new SctSubject(1, 1, array('fr' => $questions));

            // write the subject
            $sct = new Sct();
            $sct->save_sct_subject($subject);
        }

        // ---------------------------------------------------------------------
        // ADD NEW
        // ---------------------------------------------------------------------
        public function add_new()
        {
            // SCT wide parameters
            $parameters = array();
            array_push($parameters, array('name' => 'language'          , 'error_name' => 'Language'));
            array_push($parameters, array('name' => 'questions_count'   , 'error_name' => 'Questions Count'));
            array_push($parameters, array('name' => 'total_items_count' , 'error_name' => 'Total Items Count'));

            // check existence
            foreach ($parameters as $parameter)
            {
                $parameter_name = $parameter['name'];
                if (!isset($_POST[$parameter_name]))
                {
                    http_response_code(400);
                    print($parameter['error_name'] . ' is not set.');
                    exit;
                }

                ${$parameter_name} = $_POST[$parameter_name];
            }

            // check values
            if ($questions_count < 1 || $questions_count > 256)
            {
                http_response_code(400);
                print('Questions count is invalid is not set.');
                exit;
            }
            if ($total_items_count < 1 || $total_items_count > 65536)
            {
                http_response_code(400);
                print('Total items count is invalid is not set.');
                exit;
            }

            // load questions
            $sct = new Sct();
            $new_sct_id = $sct->get_new_sct_id();
            $questions = array($language => array());
            for($i = 0; $i < $questions_count; ++$i)
            {
                $this->retrieve_question($questions[$language], $i + 1, $new_sct_id);
            }

            // move files
            $subject = new SctSubject($new_sct_id, $_SESSION['user_id'], $questions);
            $sct->save_sct_subject($subject);
        }

        protected function retrieve_question(&$questions, $question_number, $new_sct_id)
        {
            $items_count = $_POST['items_' . $question_number];
            if ($items_count < 1 || $items_count > 256)
            {
                http_response_code(400);
                print('Wrong items count for question ' . $question_number . '.');
                exit;
            }

            $wording        = htmlspecialchars($_POST['wording_' . $question_number]);
            $type           = htmlspecialchars($_POST['type_' . $question_number]);
            $topics         = explode(';', htmlspecialchars($_POST['topics_' . $question_number]));
            $items          = array();

            for ($item_i = 0; $item_i < $items_count; ++$item_i)
            {
                $this->retrieve_item($items, $question_number, $item_i + 1, $new_sct_id);
            }

            $new_question = new SctQuestion($wording, $type, $topics, $items);
            array_push($questions, $new_question);
        }

        protected function retrieve_item(&$items, $question_number, $item_number, $new_sct_id)
        {
            // hypothesis
            $hypothesis = htmlspecialchars($_POST['question_hypothesis_' . $question_number . '_' . $item_number]);

            // new data
            $data_item_id   = 'question_new_data_' . $question_number . '_' . $item_number;
            $new_data = array();
            $new_data['type'] = htmlspecialchars($_POST['question_new_data_type_' . $question_number . '_' . $item_number]);

            if (isset($_POST[$data_item_id]))
            {
                $new_data['data'] = htmlspecialchars($_POST[$data_item_id]);
            }
            else if (isset($_FILES[$data_item_id]))
            {
                // check the file size
                $new_file = $_FILES[$data_item_id];
                if ($new_file['size'] > 200000000)
                {
                    return;
                }

                // create the folder
                $sct_folder_path = Router::get_base_path() . '/Content/Attachments/Sct/Sct_' . $new_sct_id;
                if (!is_dir($sct_folder_path))
                {
                    mkdir($sct_folder_path, 0777, true);
                }

                // move the file
                $new_file_path = $sct_folder_path . '/' . $question_number . '_' . $item_number;
                preg_match('/[^.]+[.]([^.]+)/', $new_file['name'], $matches);
                if (isset($matches) && count($matches) > 1)
                {
                    $new_file_path .= '.' . $matches[1];
                }

                move_uploaded_file($new_file['tmp_name'], $new_file_path);

                $new_data['data'] = $new_file_path;
            }

            $new_item = new SctItem($hypothesis, $new_data);
            array_push($items, $new_item);
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

            // sct types
            $script_content .= JavascriptGenerator::create_array('NewSctSubject', 'sctTypes', $sct_types, ['JavascriptGenerator', 'create_sct_type']);

            // sct topics
            $script_content .= JavascriptGenerator::create_array('NewSctSubject', 'sctTopics', $sct_topics, ['JavascriptGenerator', 'create_sct_topic']);

            // sct languages
            $script_content .= 'NewSctSubject.sctLanguages = [];';
            foreach ($sct_languages as $sct_language)
            {
                $new_language = '{id:' . $sct_language->id . ',name:"' . $sct_language->name . '",shortName:"' . $sct_language->short_name. '"}';

                $script_content .= 'NewSctSubject.sctLanguages.push(' . $new_language . ');';
            }

            $script = JavascriptGenerator::generate_namespace('NewSctSubject', $script_content);

            return JavascriptGenerator::enclose_in_script_tags($script);
        }
    }
?>