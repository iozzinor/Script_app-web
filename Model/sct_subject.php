<?php
    require_once 'sct_question.php';
    
    class SctSubject
    {
        /**
         * The SCT id.
         */
        private $id_;
        
        /**
         * The list of questions belonging to the SCT.
         */
        private $questions_;

        public function __construct(int $id, array $questions)
        {
            $this->id_ = $id;
            $this->questions_ = $questions;
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

        public function get_questions()
        {
            return $this->questions_;
        }
    }
?>