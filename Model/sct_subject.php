<?php
    require_once 'sct_question.php';
    
    class SctSubject
    {
        /**
         * The SCT id.
         */
        private $id_;

        /**
         * The author id.
         */
        private $author_id_;

        /**
         * The array is supposed to be associative, and the keys should be the language name.
         * The list of questions belonging to the SCT.
         */
        private $questions_;

        /**
         * The creation date in seconds since EPOCH.
         */
        private $creation_date_;

        /**
         * The edit date in seconds since EPOCH.
         */
        private $edit_date_;

        public function __construct(int $id, int $author_id, array $questions, int $creation_date = null)
        {
            $this->id_              = $id;
            $this->author_id_       = $author_id;
            $this->questions_       = $questions;

            if ($creation_date == null)
            {
                $this->creation_date_  = time(); 
            }
            else
            {
                $this->creation_date_ = $creation_date;
            }
            $this->edit_date_ = $this->creation_date_; 
        }

        public function set_id($id)
        {
            $this->id_ = $id;
        }

        public function get_id()
        {
            return $this->id_;
        }

        /**
         * Append the question to the SCT.
         * 
         * @param question The new question.
         */
        public function add_question(SctQuestion $question)
        {
            array_push($this->questions_, $question);
        }

        public function get_questions($language_name = null)
        {
            if ($language_name == null)
            {
                return $this->questions_[array_keys($this->questions_)[0]];
            }
            return $this->questions_[$language_name];
        }

        /** 
         * @return string The author last name.
         */
        public function get_author_last_name()
        {
            return 'Tartanpion';
        }

        /** 
         * @return string The author first name.
         */
        public function get_author_first_name()
        {
            return 'Jean';
        }

        /**
         * @return array The language name.
         */
        public function get_language_names()
        {
            return array_keys($this->questions_);
        }

        /**
         * @return int The creation date in seconds since EPOCH.
         */
        public function get_creation_date()
        {
            return $this->creation_date_;
        }

        /**
         * @return int The edit date in seconds since EPOCH.
         */
        public function get_edit_date()
        {
            return $this->edit_date_;
        }
    }
?>