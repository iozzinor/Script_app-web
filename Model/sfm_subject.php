<?php
    require_once 'sct_file_manager.php';
    require_once 'sct_subject.php';

    /**
     * The file manager for the SCT subject.
     */
    class SfmSubject extends SctFileManager
    {
        /**
         * The SCT subject.
         */
        private $sct_subject_;

        public function __construct(SctSubject $sct_subject)
        {
            parent::__construct($sct_subject->get_id(), 'subject');

            $this->sct_subject_ = $sct_subject;
        }

        /**
         * @return string The file content.
         */
        protected function generate_file_content()
        {
            $questions_string = $this->generate_questions();
            return $this->generate_header($questions_string) . $questions_string;
        }

        /**
         * @return string The questions in a string format.
         */
        protected function generate_questions()
        {
            $result = '';

            foreach ($this->sct_subject_->get_questions() as $question)
            {
                // number of subitems
                $result .= count($question->get_items()) . "\n";

                // sct question type
                $result .= $question->get_type() . "\n";

                // sct question topics
                $i = 0;
                $topics = $question->get_topics();
                $topics_count = count($topics);

                for ($i = 0; $i < $topics_count; ++$i)
                {
                    $result .= $topics[$i];
                    if ($i < $topics_count - 1)
                    {
                        $result .= ';';
                    }
                }
                $result .= "\n";
            }

            return $result;
        }

        /**
         * @return String The encoded languages.
         */
        protected function generate_languages_string()
        {
            $result = '';
            $i = 0;
            $language_names = $this->sct_subject_->get_language_names();
            $language_names_count = count($language_names);
            foreach ($language_names as $language_name)
            {
                $language_name = substr($language_name, 0, 256);
                $result .= $language_name;
                ++$i;

                if ($i < $language_names_count - 1)
                {
                    $result .= ';';
                }
            }
            $result .= "\n";
            return $result;
        }

        /**
         * @return string The file header.
         */
        protected function generate_header($questions_string)
        {
            $languages_string = $this->generate_languages_string();

            // file identification : SCT Subject
            $result = 'SCTS';

            // version
            $result .= pack("C", Configuration::get('sct_file_format_version'));

            // file size
            $file_size = 540 + strlen($languages_string) + strlen($questions_string);
            $resut .= pack('N', $file_size);

            // questions count
            $result .= pack('C', count($this->sct_subject_->get_questions()));

            // total items count
            $result .= pack('n', count($this->get_total_items_count()));

            // creation date - seconds since EPOCH
            $result .= pack('J', $this->sct_subject_->get_creation_date());

            // edit date
            $result .= pack('J', $this->sct_subject_->get_edit_date());

            // author
            $result .= str_pad($this->sct_subject_->get_author_last_name(), 256, chr(0));
            $result .= str_pad($this->sct_subject_->get_author_first_name(), 256, chr(0));

            // languages
            $result .= $languages_string;

            return $result;
        }

        public function get_sct_subject()
        {
            return $this->read_file();
        }

        // ---------------------------------------------------------------------
        // UTILS
        // ---------------------------------------------------------------------
        protected function get_total_items_count()
        {
            $result = 0;
            $questions = $this->sct_subject_->get_questions();
            $questions_count = count($questions);

            for ($i = 0; $i < $questions_count; ++$i)
            {
                $result += count($questions[$i]->get_items());
            }

            return $result;
        }
    }
?>