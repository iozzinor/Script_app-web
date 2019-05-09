<?php
    class SfmLanguage extends SctFileManager
    {
        private $questions_;

        public function __construct($sct_id, $language_name, $questions)
        {
            $this->questions_ = $questions;

            parent::__construct($sct_id, 'language_' . strtolower($language_name)); 
        }

        protected function generate_header()
        {
            // file identification : SCT Language
            $result = 'SCTL';

            // version
            $result .= pack("C", Configuration::get('sct_file_format_version'));

            // questions count
            $questions_count = count($this->questions_);
            $result .= pack('N', $questions_count);

            $result .= "\n";

            return $result;
        }

        protected function generate_questions()
        {
            $result = '';

            foreach ($this->questions_ as $question)
            {
                $items = $question->get_items();
                $items_count = count($items);

                // items count
                $result .= $items_count . "\n";

                // wording
                $result .= '"' . $this->prepare_text($question->get_wording()) . "\"\n";

                // items
                $this->generate_items($items, $result);
            }

            return $result;
        }

        protected function generate_items($items, &$result)
        {
            $items_count = count($items);

            for ($i = 0; $i < $items_count; ++$i)
            {
                $item = $items[$i];

                // hypothesis
                $result .= '"' . $this->prepare_text($item->get_hypothesis()) . "\"\n";

                // new data
                $new_data = $item->get_new_data();
                $result .= $new_data['type'] . ':"' . $this->prepare_text($new_data['data']) . "\"\n";
            }
        }   

        protected function generate_file_content()
        {
            return $this->generate_header() . $this->generate_questions();
        }

        /**
         * Escape double quotes.
         * 
         * @return string The text to store.
         */
        protected function prepare_text($text)
        {
            return str_replace('"', '\"', $text);
        }
    }
?>