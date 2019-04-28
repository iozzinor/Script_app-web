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
            return 'test';
        }

        /**
         * @return string The questions in a string format.
         */
        protected function generate_questions()
        {
            $result = '';

            foreach ($this->sct_subject_->get_questions() as $question)
            {
                $result .= $question;
            }

            return $result;
        }

        /**
         * @return string The file header.
         */
        protected function generate_header()
        {
            // file identification : SCT Subject
            $result = "SCTS";

            // version
            $result .= pack("C", Configuration::get('sct_file_format_version'));

            // file size

            // questions count

            // items count


            return $result;
        }

        public function get_sct_subject()
        {
            return $this->read_file();
        }
    }
?>